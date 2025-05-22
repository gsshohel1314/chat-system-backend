<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Traits\ApiResponse;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;

class ChatController extends Controller
{
    use ApiResponse;
    
    // Get messages of a conversation
    public function messages(Request $request, Conversation $conversation): JsonResponse
    {
        $userId = $request->user()->id;

        // Authorization: only users in the conversation
        if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
            return $this->errorResponse('Unauthorized access to messages', [], 403);
        }

        $messages = $conversation->messages()->with('sender:id,name')->orderBy('created_at')->get();

        return $this->successResponse(
            MessageResource::collection($messages),
            'Messages fetched successfully'
        );
    }

    // Send a new message in a conversation
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userId = $request->user()->id;

        // Authorization: only users in the conversation
        if (!in_array($userId, [$conversation->user_one_id, $conversation->user_two_id])) {
            return $this->errorResponse('Unauthorized to send message', [], 403);
        }

        // Determine the receiver ID
        $receiverId = $conversation->user_one_id == $userId ? $conversation->user_two_id : $conversation->user_one_id;

        $chat = Chat::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'receiver_id' => $receiverId,
            'message' => $request->message,
        ]);

        // TODO: Broadcast the message event here (later)

        return $this->successResponse(
            new MessageResource($chat->load('sender:id,name')),
            'Message sent successfully',
            201
        );
    }
}
