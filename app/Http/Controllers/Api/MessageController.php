<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\User;
use App\Events\ChatEvent;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1'
        ]);

        $query = $request->input('q');

        $users = User::where('id', '!=', $request->user()->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%");
            })
            ->select('id', 'name', 'email')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function getConversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = Conversation::where(function ($q) use ($userId) {
            $q->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId);
        })
        ->with([
            'userOne:id,name,email',
            'userTwo:id,name,email',
            'messages' => function($q){
                $q->latest()->limit(1);
            }
        ])
        ->latest()
        ->get();

        return response()->json($conversations);
    }

    public function getMessages(Request $request, Conversation $conversation): JsonResponse
    {
        $userId = $request->user()->id;

        // Authorization: only users in the conversation
        if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()->with('sender:id,name')->orderBy('created_at')->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $senderId = $request->user()->id;
        $receiverId = $request->receiver_id;

        $conversation = Conversation::where(function ($query) use ($senderId, $receiverId) {
            $query->where('user_one_id', $senderId)->where('user_two_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('user_one_id', $receiverId)->where('user_two_id', $senderId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $senderId,
                'user_two_id' => $receiverId,
            ]);
        }

        $chat = Chat::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        // broadcast event
        broadcast(new ChatEvent($chat))->toOthers();

        return response()->json($chat);
    }
}
