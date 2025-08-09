// Import Echo and Pusher for use with Laravel
import Echo from 'laravel-echo';
import Pusher from 'pusher-js'; // Import Pusher

// Ensure Pusher is globally available
window.Pusher = Pusher;

// Set up Echo globally
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY, // Your Pusher key from environment variables
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER, // Pusher cluster
    forceTLS: true, // Enforce TLS (important for security)
    encrypted: true // Optional but recommended for encryption
});

