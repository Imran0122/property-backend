<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name',
        'slug',
        'image',
        'description',
        'views',
        'is_popular',
    ];

    protected $casts = [
        'views' => 'integer',
        'is_popular' => 'boolean',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function images()
    {
        return $this->hasMany(SocietyImage::class, 'society_id');
    }
}