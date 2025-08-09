@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Inbox</h2>

        <!-- Button to start a new conversation -->
        <div class="mb-6">
            <a href="{{ route('conversation.create') }}"
                class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 transition duration-200">
                <i class="fas fa-plus mr-2"></i> Start New Conversation
            </a>
        </div>

        <!-- Conversation List -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($conversations as $conversation)
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300">
                    <a href="{{ route('chat.view', $conversation->id) }}" class="block text-blue-600 hover:text-blue-800">
                        <h3 class="text-xl font-semibold">{{ $conversation->name }}</h3>
                        <p class="text-sm text-gray-500 mt-2">{{ $conversation->users->count() }} participants</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
