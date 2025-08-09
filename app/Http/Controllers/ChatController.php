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
        // Validate the message input
        $request->validate([
            'message' => 'required|string|max:1000',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,docx|max:2048', // Validate file types and size
        ]);

        // Create a new message in the database
        $message = Message::create([
            'user_id' => Auth::id(),
            'conversation_id' => $conversationId,
            'content' => $request->message,
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('chat_files', 'public');
            $message->file_path = $path;
            $message->save();
        }

        $message->conversation->users->each(function ($user) use ($message) {
            if ($user->id !== Auth::id()) {
                $user->notify(new NewMessageNotification($message));
            }
        });

        // Broadcast the message event (real-time update)
        broadcast(new MessageSent($message));

        // Return JSON response after the message is sent
        return response()->json(['status' => 'Message sent successfully!', 'message' => $message]);
    }


}
