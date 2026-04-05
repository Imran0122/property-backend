<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
    protected $fillable = [
        'city_id',
        'name',
        'slug',
        'image',
        'description',
        'is_popular',
        'views',
        'plot_finder_url',
        'map_url',
        'google_map_url',
        'location_url',
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function images()
    {
        return $this->hasMany(SocietyImage::class, 'society_id')->orderBy('sort_order');
    }
}