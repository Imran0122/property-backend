<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',                // House / Plot / Commercial (legacy)
        'location',            // full address
        'city_id',             // FK -> cities
        'area',                // area size (string/number)
        'bedrooms',
        'bathrooms',
        'price',
        'status',              // available / sold / rented
        'is_featured',
        'property_type_id',    // FK -> property_types
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
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // City
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    // Type (House / Plot / Commercial category table)
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id', 'id');
    }

    // Images
    public function images()
    {
        return $this->hasMany(PropertyImage::class, 'property_id', 'id');
    }

    // Many-to-Many Amenities
    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity', 'property_id', 'amenity_id');
    }

    // Leads / Inquiries
    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'property_id', 'id');
    }

    // Favorited by users
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'property_id', 'user_id')
            ->withTimestamps();
    }

    // Additional Features (area, furnished, etc.)
    public function features()
    {
        return $this->hasOne(PropertyFeature::class, 'property_id', 'id');
    }
    public function favourites()
{
    return $this->hasMany(\App\Models\Favourite::class);
}


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    // Return a full URL for main image
    public function getMainImageAttribute()
    {
        $img = $this->images->first();
        if (!$img) return null;

        // if image model already has "url" accessor
        if (isset($img->url)) return $img->url;

        // fallback
        return asset('storage/' . $img->image_path);
    }
    protected static function booted()
{
    static::creating(function ($property) {
        $property->slug = \Str::slug($property->title) . '-' . time();
    });
}
}
