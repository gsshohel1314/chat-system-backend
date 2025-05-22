<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $guarded = ['id'];

    // User One relation
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    // User Two relation
    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    // Messages in this conversation
    public function messages()
    {
        return $this->hasMany(Chat::class, 'conversation_id');
    }
}
