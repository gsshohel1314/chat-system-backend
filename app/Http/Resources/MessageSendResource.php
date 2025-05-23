<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageSendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation->id,
            'convarsation'  => [
                'id'          => $this->conversation->id,
                'user_one_id' => $this->conversation->user_one_id,
                'user_two_id' => $this->conversation->user_two_id,
            ],
            'sender_id' => $this->sender->id,
            'sender' => [
                'id'    => $this->sender->id,
                'name'  => $this->sender->name,
                'email' => $this->sender->email,
            ],
            'receiver_id' => $this->receiver->id,
            'receiver' => [
                'id'    => $this->receiver->id,
                'name'  => $this->receiver->name,
                'email' => $this->receiver->email,
            ],
            'message'     => $this->message,
            'created_at'  => $this->created_at->toDateTimeString(),
            'updated_at'  => $this->updated_at->toDateTimeString(),
        ];
    }
}
