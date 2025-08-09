@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl mb-4">Login</h2>
    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" class="w-full p-2 mb-4 border border-gray-300 rounded" required>
        <input type="password" name="password" placeholder="Password" class="w-full p-2 mb-4 border border-gray-300 rounded" required>
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
    </form>
</div>
@endsection
