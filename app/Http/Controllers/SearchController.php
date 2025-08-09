<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query');
        $conversations = Conversation::where('name', 'like', "%{$query}%")->get();
        $messages = Message::where('content', 'like', "%{$query}%")->get();

        return view('search.results', compact('conversations', 'messages'));
    }

}
