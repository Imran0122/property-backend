<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'refresh_credits', 'hot_credits',
        'super_hot_credits', 'story_credits',
        'photo_credits', 'video_credits',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}