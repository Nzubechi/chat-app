<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Events\MessageSent;
use App\Events\TypingEvent;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewMessageNotification;

class ChatController extends Controller
{
    // Display the list of conversations (Inbox)
    public function inbox()
    {
        // Fetch all conversations with associated users
        $conversations = Conversation::with('users')->get();
        $allusers = User::all();
        return view('chat.inbox', compact('conversations', 'allusers'));
    }

    // Display the individual chat view for a conversation
    public function chatView($conversationId)
    {
        // Fetch the conversation by ID
        $conversation = Conversation::findOrFail($conversationId);

        // Fetch messages associated with this conversation
        $messages = $conversation->messages;
        $allusers = User::all();

        // Return the chat view with the conversation and messages
        return view('chat.view', compact('conversation', 'messages', 'allusers'));
    }

    public function sendTypingIndicator(Request $request, $conversationId)
    {
        broadcast(new TypingEvent($conversationId, Auth::user()->name));

        return response()->json(['status' => 'success']);
    }

    public function sendMessage(Request $request, $conversationId)
    {
        try {
            // Validate the message input
            $request->validate([
                'message' => 'required|string|max:1000', // Make the file optional
            ]);

            if ($request->hasFile('file')) {
                $request->validate([
                    'file' => 'file|mimes:jpeg,png,jpg,pdf,docx|max:2048',
                ]);
            }

            // Create a new message in the database
            $message = Message::create([
                'user_id' => Auth::id(),
                'conversation_id' => $conversationId,
                'content' => $request->message,
            ]);

            // Handle file upload if a file is provided
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('chat_files', 'public');
                $message->file_path = $path;
                $message->save();
            }

            // Notify users in the conversation (excluding the sender)
            $message->conversation->users->each(function ($user) use ($message) {
                if ($user->id !== Auth::id()) {
                    $user->notify(new NewMessageNotification($message));
                }
            });

            // Broadcast the message event (real-time update)
            broadcast(new MessageSent($message));

            // Return a successful response with the message data
            return response()->json([
                'status' => 'Message sent successfully!',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error sending message: ' . $e->getMessage());

            // Return a response with the error message
            return response()->json([
                'status' => 'error',
                'message' => 'There was an issue sending the message. Please try again later.',
                'error' => $e->getMessage(), // Include the error message for debugging
            ], 500); // 500 status code for internal server error
        }
    }




}
