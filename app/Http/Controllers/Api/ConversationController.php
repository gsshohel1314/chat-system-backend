<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationListResource;
use App\Http\Resources\ConversationDetailResource;

class ConversationController extends Controller
{
    use ApiResponse;
    
    // List all conversations of the authenticated user
    public function index(Request $request): JsonResponse
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

        return $this->successResponse(
            ConversationListResource::collection($conversations),
            'Conversations fetched successfully'
        );
    }

    // Start or get existing conversation between two users
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id|not_in:' . $request->user()->id,
        ]);

        $senderId = $request->user()->id;
        $receiverId = $request->receiver_id;

        $conversation = Conversation::where(function ($query) use ($senderId, $receiverId) {
            $query->where('user_one_id', $senderId)
                ->where('user_two_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('user_one_id', $receiverId)
                ->where('user_two_id', $senderId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $senderId,
                'user_two_id' => $receiverId,
            ]);
        }

        return $this->successResponse(
            new ConversationDetailResource($conversation),
            'Conversation started successfully'
        );
    }
}
