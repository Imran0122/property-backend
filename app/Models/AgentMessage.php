<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentMessage extends Model
{
      use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
    ];

    // Relation with Property
    public function property()
    {
        return $this->belongsTo(\App\Models\Property::class);
    }
    public function replies()
{
    return $this->hasMany(AgentReply::class, 'agent_message_id');
}
}

