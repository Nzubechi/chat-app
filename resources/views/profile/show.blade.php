@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h2 class="text-2xl font-semibold mb-6">Your Profile</h2>

        @if (session('success'))
            <div class="mb-4 text-green-600">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                    class="w-full p-3 mt-2 border border-gray-300 rounded-md" required>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                    class="w-full p-3 mt-2 border border-gray-300 rounded-md" required>
            </div>

            <!-- Avatar -->
            <div class="mb-4">
                <label for="avatar" class="block text-sm font-semibold text-gray-700">Profile Picture</label>
                <input type="file" name="avatar" id="avatar"
                    class="w-full p-3 mt-2 border border-gray-300 rounded-md">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-md hover:bg-blue-700">Update
                Profile</button>
        </form>
    </div>
@endsection
