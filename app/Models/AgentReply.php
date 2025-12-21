<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_message_id',
        'reply_message',
    ];

    public function message()
    {
        return $this->belongsTo(AgentMessage::class, 'agent_message_id');
    }
}
