@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
    @endphp
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Conversation Title and ID -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl">Conversation: {{ $conversation->name }}</h2>

            <!-- Toggle Buttons for Search and Add Participants Form -->
            <div class="space-x-4">
                <button id="searchToggleButton" class="bg-gray-800 text-white p-2 text-sm rounded-md">
                    Search Messages
                </button>
                <button id="addParticipantsToggleButton" class="bg-gray-600 text-white p-2 text-sm rounded-md">
                    Add Participants
                </button>
            </div>
        </div>


        <!-- Search Form (Initially Hidden) -->
        <div id="searchForm" class="hidden mb-6">
            <h3 class="text-lg font-semibold mb-4">Search Messages</h3>
            <form method="GET" action="{{ route('search') }}">
                <input type="text" name="query" placeholder="Search conversations and messages"
                    class="w-full p-3 border border-gray-300 rounded-md mt-4">
                <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded mt-4">Search</button>
            </form>
        </div>

        <!-- Add Participants Form (Initially Hidden) -->
        @if ($user->role === 'admin')
            <div id="addParticipantsForm" class="hidden mb-6">
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

        <div class="flex flex-col h-[calc(100vh-280px)]"> <!-- Adjusting height between fixed navbar and footer -->
            <!-- Messages Container (Top, fills the remaining space) -->
            <div class="space-y-4 mb-4 flex-grow overflow-y-auto max-h-[calc(100vh-160px)]" id="message-container">
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

            <!-- Message Form (Bottom, stays at the bottom of the remaining space) -->
            <form id="messageForm" method="POST" enctype="multipart/form-data" class="flex items-center py-4 bg-white">
                @csrf

                <!-- Message Input -->
                <input type="text" id="messageInput" name="message" placeholder="Type your message"
                    class="p-2 w-full border border-gray-300 rounded-md" required>

                <!-- File Input for Attachments (Hidden, triggered by button) -->
                <input type="file" id="fileInput" name="file" class="hidden" onchange="updateFileName()">

                <!-- Custom File Upload Button -->
                <button type="button" id="fileButton" class="bg-slate-500 text-white p-2 rounded-md mx-2">
                    Upload
                </button>

                <!-- Send Button -->
                <button type="submit" class="bg-blue-600 text-white p-2 rounded-md">Send</button>
            </form>
        </div>


        <script>
            // Trigger the file input when the "Choose File" button is clicked
            document.getElementById('fileButton').addEventListener('click', function() {
                document.getElementById('fileInput').click();
            });

            // Optionally, update the file button text when a file is chosen (you can display the file name)
            function updateFileName() {
                const fileInput = document.getElementById('fileInput');
                const fileButton = document.getElementById('fileButton');
                if (fileInput.files.length > 0) {
                    fileButton.innerText = fileInput.files[0].name;
                } else {
                    fileButton.innerText = 'Choose File';
                }
            }
        </script>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            messageContainer = document.getElementById('message-container')
            messageContainer.scrollTop = messageContainer.scrollHeight;
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
                            `<div class="mt-2"><a href="{{ asset('storage/' . $message->file_path) }}" target="_blank" class="text-blue-600">View File</a></div>`;
                    }

                    // Append new message to the message container
                    messageContainer = document.getElementById('message-container')
                    messageContainer.appendChild(messageElement);
                    messageContainer.scrollTop = messageContainer.scrollHeight;
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle visibility of the Search Form
            const searchToggleButton = document.getElementById('searchToggleButton');
            const searchForm = document.getElementById('searchForm');

            searchToggleButton.addEventListener('click', function() {
                searchForm.classList.toggle('hidden');
            });

            // Toggle visibility of the Add Participants Form
            const addParticipantsToggleButton = document.getElementById('addParticipantsToggleButton');
            const addParticipantsForm = document.getElementById('addParticipantsForm');

            addParticipantsToggleButton.addEventListener('click', function() {
                addParticipantsForm.classList.toggle('hidden');
            });

            // The rest of your existing code for Echo, message submission, etc.
        });
    </script>
@endsection
