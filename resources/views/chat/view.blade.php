@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto px-4">
        <h2 class="text-2xl mb-4">Conversation: {{ $conversation->name }} - {{ $conversation->id }}</h2>

        <!-- Messages Container -->
        <div class="space-y-4 mb-4" id="message-container">
            @foreach ($messages as $message)
                <div class="p-4 bg-gray-200 rounded shadow" id="message-{{ $message->id }}">
                    <strong>{{ $message->user->name }}: </strong>
                    <p>{{ $message->content }}</p>
                </div>
            @endforeach
        </div>

        <!-- Message Form -->
        <form id="messageForm">
            @csrf
            <input type="text" id="messageInput" name="message" placeholder="Type your message"
                class="w-full p-2 border border-gray-300 rounded mt-2" required>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded mt-2">Send</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Subscribe to the conversation channel using Echo
            window.Echo.channel(`conversation.{{ $conversation->id }}`)
                .listen('MessageSent', (event) => {
                    const message = event.message;
                    console.log("Message:", event);


                    // Create a new message element and append to the container
                    const messageElement = document.createElement('div');
                    messageElement.id = `message-${message.id}`;
                    messageElement.classList.add('p-4', 'bg-gray-200', 'rounded', 'shadow');
                    messageElement.innerHTML = `<strong>${message.user.name}:</strong> <p>${message.content}</p>`;

                    // Append new message to the message container
                    document.getElementById('message-container').appendChild(messageElement);
                })
                .subscribed(() => {
                    console.log('Successfully subscribed to the conversation channel.');
                })
                .error((error) => {
                    console.error('Subscription error:', error);
                });

            // Handle message form submission using AJAX
            const messageForm = document.getElementById('messageForm');
            messageForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent page refresh on form submission

                const messageContent = document.getElementById('messageInput').value;
                const conversationId = '{{ $conversation->id }}'; // Get the conversation ID from Blade

                // Send the message via AJAX
                fetch('{{ route('send.message', $conversation->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message: messageContent,
                        conversation_id: conversationId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);

                    // Clear the input field after sending the message
                    document.getElementById('messageInput').value = '';
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                });
            });
        });
    </script>
@endsection
