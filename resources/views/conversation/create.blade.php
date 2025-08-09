@extends('layouts.app')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6 text-center">Create New Conversation</h2>

        <!-- Create Conversation Form -->
        <form method="POST" action="{{ route('conversation.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold text-gray-700">Conversation Name</label>
                <input type="text" name="name" id="name" placeholder="Enter conversation name"
                    class="w-full p-3 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-blue-600 text-white p-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Create Conversation
            </button>
        </form>
    </div>
@endsection
