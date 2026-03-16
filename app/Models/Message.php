<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
class Message extends Model
{

use SoftDeletes;
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'property_id',
        'body',
        'is_read',
        'is_trashed_by_receiver',
        'trashed_at',
        'type',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_trashed_by_receiver' => 'boolean',
        'trashed_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}