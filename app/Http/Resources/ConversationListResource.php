<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'user_one' => [
                'id'    => $this->userOne->id,
                'name'  => $this->userOne->name,
                'email' => $this->userOne->email,
            ],
            'user_two' => [
                'id'    => $this->userTwo->id,
                'name'  => $this->userTwo->name,
                'email' => $this->userTwo->email,
            ],
            'last_message' => $this->messages()->latest()->first(),
            'created_at'   => $this->created_at->toDateTimeString(),
            'updated_at'   => $this->updated_at->toDateTimeString(),
        ];
    }
}
