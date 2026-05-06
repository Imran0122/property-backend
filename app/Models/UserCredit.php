<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'announcement_credits',
        'refresh_credits',
        'hot_credits',
        'super_hot_credits',
        'story_credits',
        'photo_credits',
        'video_credits',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Credit type se column name map karo
     */
    public static function creditColumnForType(string $type): ?string
    {
        $type = strtolower(trim($type));

        return match(true) {
            str_contains($type, 'announcement')                                    => 'announcement_credits',
            str_contains($type, 'super') && str_contains($type, 'hot')            => 'super_hot_credits',
            str_contains($type, 'hot')                                             => 'hot_credits',
            str_contains($type, 'refresh')                                         => 'refresh_credits',
            str_contains($type, 'story')                                           => 'story_credits',
            str_contains($type, 'photo') || str_contains($type, 'photograph')     => 'photo_credits',
            str_contains($type, 'video') || str_contains($type, 'videography')    => 'video_credits',
            default                                                                 => null,
        };
    }
}