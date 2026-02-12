<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',

        'city_id',
        'area_id',              // location id

        'property_type_id',     // House / Flat / Plot / Commercial

        'area',
        'area_size',
        'area_unit',

        'bedrooms',
        'bathrooms',

        'price',

        'purpose',              // sale / rent
        'status',               // active / inactive

        'is_featured',
        'featured_until',

        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'featured_until' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Agent / Owner
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // City
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Location (Area)
    public function location()
    {
        return $this->belongsTo(Location::class, 'area_id');
    }

    // Property Type
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    // Images
    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    // Amenities
    public function amenities()
    {
        return $this->belongsToMany(
            Amenity::class,
            'property_amenity',
            'property_id',
            'amenity_id'
        );
    }

    // Inquiries
    public function inquiries()
    {
        return $this->hasMany(PropertyInquiry::class);
    }

    // Favorites
    public function favoritedBy()
    {
        return $this->belongsToMany(
            User::class,
            'favorites',
            'property_id',
            'user_id'
        )->withTimestamps();
    }

    // Extra Features
    public function features()
    {
        return $this->hasOne(PropertyFeature::class);
    }

 

    public function getMainImageAttribute()
    {
        $img = $this->images()->where('is_primary', 1)->first();

        if (!$img) return null;

        return asset('storage/' . $img->image_path);
    }

    

    protected static function booted()
    {
        static::creating(function ($property) {
            $property->slug = Str::slug($property->title) . '-' . time();
        });
    }
}
