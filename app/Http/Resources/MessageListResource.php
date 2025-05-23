<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageListResource extends JsonResource
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
            'convarsation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
            'receiver_id' => $this->receiver->id,
            'sender' => [
                'id'    => $this->sender->id,
                'name'  => $this->sender->name,
                'email' => $this->sender->email,
            ],
            'message'     => $this->message,
            'created_at'  => $this->created_at->toDateTimeString(),
            'updated_at'  => $this->updated_at->toDateTimeString(),
        ];
    }
}
