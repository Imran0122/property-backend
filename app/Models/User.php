<?php

namespace App\Models;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasApiTokens, HasFactory, Notifiable;
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    //     'role',      // e.g. agent, admin, buyer
    //     'is_admin',
    // ];

protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'role',
        'is_admin',
        'landline',
        'whatsapp',
        'city_id',
        'address',
        'profile_image',
        'currency',
        'area_unit',
        'email_notifications',
        'newsletters',
        'automated_reports'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'newsletters' => 'boolean',
        'automated_reports' => 'boolean',
        'city_id' => 'integer',
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

public function boutiqueCartItems()
{
    return $this->hasMany(\App\Models\BoutiqueCartItem::class);
}

public function boutiqueOrders()
{
    return $this->hasMany(\App\Models\BoutiqueOrder::class);
}
}
