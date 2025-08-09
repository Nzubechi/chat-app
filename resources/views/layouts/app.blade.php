<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workcity Chat</title>

    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Vite CSS (app.css) -->
    @vite('resources/css/app.css')
    <!-- Vite JS (app.js) -->
    @vite('resources/js/app.js')
</head>

<body class="flex flex-col min-h-screen">

    <!-- Main Navbar -->
    <header class="bg-blue-600 text-white p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="/" class="text-2xl font-bold">Workcity Chat</a>
            <div class="space-x-4">
                @auth
                    <a href="{{ route('inbox') }}" class="hover:underline">Inbox</a>
                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="hover:underline">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Login</a>
                    <a href="{{ route('signup') }}" class="hover:underline">Sign Up</a>
                @endauth
            </div>
        </div>
    </header>


    <!-- Main Content Section -->
    <main class="flex-grow py-4">
        @yield('content') <!-- Content injected here -->
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center">
        <p>&copy; 2025 Workcity Africa</p>
    </footer>
</body>

</html>
