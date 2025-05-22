<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $guarded = ['id'];

    // Sender user relation
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Receiver user relation
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Conversation this chat belongs to
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
