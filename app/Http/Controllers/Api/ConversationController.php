<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationListResource;

class ConversationController extends Controller
{
    use ApiResponse;

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

        return $this->successResponse(
            ConversationListResource::collection($conversations),
            'Conversations fetched successfully'
        );
    }
}
