<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }
    
    public function broadcastOn()
    {
        return new Channel('chat-channel'); 
    }

    public function broadcastAs()
    {
        return 'ChatEvent';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->chat->id,
            'conversation_id' => $this->chat->conversation->id,
            'sender_id' => $this->chat->sender_id,
            'receiver_id' => $this->chat->receiver_id,
            'sender' => $this->chat->sender,
            'receiver' => $this->chat->receiver,
            'message' => $this->chat->message,
            'created_at' => $this->chat->created_at,
            'updated_at' => $this->chat->updated_at,
        ];
    }
}
