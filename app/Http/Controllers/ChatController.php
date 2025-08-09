<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Events\MessageSent;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // Display the list of conversations (Inbox)
    public function inbox()
    {
        // Fetch all conversations with associated users
        $conversations = Conversation::with('users')->get();
        return view('chat.inbox', compact('conversations'));
    }

    // Display the individual chat view for a conversation
    public function chatView($conversationId)
    {
        // Fetch the conversation by ID
        $conversation = Conversation::findOrFail($conversationId);

        // Fetch messages associated with this conversation
        $messages = $conversation->messages;

        // Return the chat view with the conversation and messages
        return view('chat.view', compact('conversation', 'messages'));
    }

    public function sendMessage(Request $request, $conversationId)
    {
        // Validate the message input
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Create a new message in the database
        $message = Message::create([
            'user_id' => Auth::id(),
            'conversation_id' => $conversationId,
            'content' => $request->message,
        ]);

        // Broadcast the message event (real-time update)
        broadcast(new MessageSent($message));

        // Return JSON response after the message is sent
        return response()->json(['status' => 'Message sent successfully!', 'message' => $message]);
    }


}
