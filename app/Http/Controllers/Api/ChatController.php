<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Events\ChatEvent;
use App\Traits\ApiResponse;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageListResource;
use App\Http\Resources\MessageSendResource;

class ChatController extends Controller
{
    use ApiResponse;

    public function getMessages(Request $request, Conversation $conversation): JsonResponse
    {
        $userId = $request->user()->id;

        // Authorization: only users in the conversation
        if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
            return $this->errorResponse('Unauthorized access to messages', [], 403);
        }

        $messages = $conversation->messages()->with('sender:id,name')->orderBy('created_at')->get();

        return $this->successResponse(
            MessageListResource::collection($messages),
            'Messages fetched successfully'
        );
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

        return $this->successResponse(
            new MessageSendResource($chat->load('sender:id,name,email')),
            'Message sent successfully',
            201
        );
    }
}
