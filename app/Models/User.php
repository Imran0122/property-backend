<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin',
        'status',
        'is_agent',
        'phone',
        'mobile',
        'landline',
        'whatsapp',
        'agency_name',
        'agent_photo',
        'bio',
        'agency_id',
        'city_id',
        'address',
        'profile_image',
        'currency',
        'area_unit',
        'email_notifications',
        'newsletters',
        'automated_reports',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_agent' => 'boolean',
        'agency_id' => 'integer',
        'city_id' => 'integer',
        'email_notifications' => 'boolean',
        'newsletters' => 'boolean',
        'automated_reports' => 'boolean',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class, 'user_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

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

    public function boutiqueCartItems()
    {
        return $this->hasMany(\App\Models\BoutiqueCartItem::class);
    }

    public function boutiqueOrders()
    {
        return $this->hasMany(\App\Models\BoutiqueOrder::class);
    }
}