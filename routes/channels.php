<?php

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversation_id}', function (Message $message, Conversation $conversation) {
    return (int) $message->conversation->id === (int) $conversation->id;
});
