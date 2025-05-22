<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ConversationController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Search users by name or email
        Route::get('/users/search', [UserController::class, 'search']);
    
        // Conversations: list and start new
        Route::get('/conversations-list', [ConversationController::class, 'index']);
        Route::post('/conversations/start', [ConversationController::class, 'start']);

        // Chats: get messages and send new message
        Route::get('/conversations/{conversation}/messages', [ChatController::class, 'messages']);
        Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);






        Route::get('/search-users', [MessageController::class, 'searchUsers']);
        Route::get('/conversations', [MessageController::class, 'getConversations']);
        Route::get('/messages/{conversation}', [MessageController::class, 'getMessages']);
        Route::post('/messages', [MessageController::class, 'sendMessage']);
    });
});
