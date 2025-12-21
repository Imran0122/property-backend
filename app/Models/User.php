<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',      // e.g. agent, admin, buyer
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 🔗 Relationships

    // User can have many Properties (if seller/agent)
   public function properties()
{
    return $this->hasMany(Property::class, 'user_id');
}


    // User can send many Inquiries (if buyer)
    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    // User can have many Favorites (Saved Properties)
 public function favourites()
{
    return $this->hasMany(\App\Models\Favourite::class);
}

public function agency()
{
    return $this->belongsTo(Agency::class);
}

public function favorites()
{
    return $this->belongsToMany(
        \App\Models\Property::class,
        'favorites'
    )->withTimestamps();
}
}
