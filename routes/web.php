<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConversationController;

Route::get('/', [AuthController::class, 'showSignupForm'])->name('start');
Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.submit');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');


// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/inbox', [ChatController::class, 'inbox'])->name('inbox');
    Route::get('/chat/{conversationId}', [ChatController::class, 'chatView'])->name('chat.view');
    Route::post('/chat/{conversationId}/send', [ChatController::class, 'sendMessage'])->name('send.message');
    Route::get('/search', [SearchController::class, 'search'])->name('search');

    Route::post('/logout', function () {
        Auth::logout();  // Log the user out
        return redirect()->route('login');  // Redirect to login page
    })->name('logout');

    Route::get('/conversation/create', [ConversationController::class, 'create'])->name('conversation.create');
    Route::post('/conversation', [ConversationController::class, 'store'])->name('conversation.store');
    Route::post('/conversation/{conversationId}/addParticipants', [ConversationController::class, 'addParticipants'])
        ->name('conversation.addParticipants');

    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

    Route::middleware(['isAdmin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    });


});
