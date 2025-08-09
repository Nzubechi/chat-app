@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
    @endphp
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Conversation Title and ID -->
        <h2 class="text-2xl mb-4">Conversation: {{ $conversation->name }}</h2>

        <!-- Search Form (Optional placement) -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Search Messages</h3>
            <form method="GET" action="{{ route('search') }}">
                <input type="text" name="query" placeholder="Search conversations and messages"
                    class="w-full p-3 border border-gray-300 rounded-md mt-4">
                <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded mt-4">Search</button>
            </form>
        </div>

        <!-- Add Participants Form (Visible to Admin or relevant roles) -->
        @if ($user->role === 'admin')
            <!-- Optional: Show form to admins only -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4">Add Participants to Conversation</h3>
                <form method="POST" action="{{ route('conversation.addParticipants', $conversation->id) }}">
                    @csrf
                    <select name="user_ids[]" multiple class="w-full p-3 mt-2 border border-gray-300 rounded-md">
                        @foreach ($allusers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-md mt-4">Add
                        Participants</button>
                </form>
            </div>
        @endif

        <!-- Messages Container -->
        <div class="space-y-4 mb-4" id="message-container">
            @foreach ($messages as $message)
                <div class="p-4 bg-gray-200 rounded shadow" id="message-{{ $message->id }}">
                    <strong>{{ $message->user->name }}: </strong>
                    <p>{{ $message->content }}</p>
                    @if ($message->file_path)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank"
                                class="text-blue-600">View File</a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Message Form (Send New Message) -->
        <form id="messageForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" id="messageInput" name="message" placeholder="Type your message"
                class="w-full p-2 border border-gray-300 rounded mt-2" required>

            <!-- File Input for Attachments -->
            <input type="file" id="fileInput" name="file" class="mt-2 p-3 rounded-md">

            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded mt-2">Send</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Subscribe to the conversation channel using Echo
            window.Echo.channel(`conversation.{{ $conversation->id }}`)
                .listen('MessageSent', (event) => {
                    const message = event.message;
                    console.log("Message:", event);

                    // Create a new message element and append to the container
                    const messageElement = document.createElement('div');
                    messageElement.id = `message-${message.id}`;
                    messageElement.classList.add('p-4', 'bg-gray-200', 'rounded', 'shadow');
                    messageElement.innerHTML =
                        `<strong>${message.user.name}:</strong> <p>${message.content}</p>`;

                    // If there is a file, display the download link
                    if (message.file_path) {
                        messageElement.innerHTML +=
                            `<div class="mt-2"><a href="${message.file_path}" target="_blank" class="text-blue-600">View File</a></div>`;
                    }

                    // Append new message to the message container
                    document.getElementById('message-container').appendChild(messageElement);
                })
                .subscribed(() => {
                    console.log('Successfully subscribed to the conversation channel.');
                })
                .error((error) => {
                    console.error('Subscription error:', error);
                });

            window.Echo.channel(`conversation.{{ $conversation->id }}`)
                .listen('TypingEvent', (event) => {
                    document.getElementById('typing-indicator').innerText = `${event.user.name} is typing...`;
                });

            window.Echo.private(`user.'{{ $user->id }}`)
                .listen('NewMessageNotification', (event) => {
                    // Display a notification for the new message
                    alert("event.message.content");
                });


            // Handle message form submission using AJAX
            const messageForm = document.getElementById('messageForm');
            messageForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent page refresh on form submission

                const messageContent = document.getElementById('messageInput').value;
                const conversationId = '{{ $conversation->id }}'; // Get the conversation ID from Blade
                const fileInput = document.getElementById('fileInput').files[0]; // Get the selected file

                // Create a FormData object to send the message and file
                const formData = new FormData();
                formData.append('message', messageContent);
                formData.append('file', fileInput); // Append the file to the request
                formData.append('conversation_id', conversationId);
                formData.append('_token', '{{ csrf_token() }}'); // Append CSRF token

                // Send the message via AJAX
                fetch('{{ route('send.message', $conversation->id) }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);

                        // Clear the input field after sending the message
                        document.getElementById('messageInput').value = '';
                        document.getElementById('fileInput').value = ''; // Clear the file input
                    })
                    .catch(error => {
                        console.error('Error sending message:', error);
                    });
            });
        });
    </script>
@endsection
