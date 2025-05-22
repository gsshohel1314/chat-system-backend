<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'conversation_id'=> $this->conversation_id,
            'sender'         => [
                'id'   => $this->sender->id,
                'name' => $this->sender->name,
            ],
            'receiver_id'    => $this->receiver_id,
            'message'        => $this->message,
            'created_at'     => $this->created_at->toDateTimeString(),
            'updated_at'     => $this->updated_at->toDateTimeString(),
        ];
    }
}
