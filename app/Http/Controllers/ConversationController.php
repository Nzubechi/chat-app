<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    // Show the form to create a new conversation
    public function create()
    {
        return view('conversation.create');
    }

    // Store the new conversation in the database
    public function store(Request $request)
    {
        // Validate the conversation name
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create a new conversation
        $conversation = Conversation::create([
            'name' => $request->name,
        ]);

        // Attach the current user as a participant in the conversation
        $conversation->users()->attach(Auth::id());

        // Redirect to the inbox or the newly created conversation
        return redirect()->route('chat.view', $conversation->id);
    }
}

