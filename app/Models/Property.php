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
        'area_id',
        'property_type_id',
        'area',
        'area_size',
        'area_unit',
        'bedrooms',
        'bathrooms',
        'price',
        'purpose',
        'status',
        'is_featured',
        'is_hot',
        'is_super_hot',
        'featured_until',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_hot' => 'boolean',
        'is_super_hot' => 'boolean',
        'featured_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function areaDetail()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    // backward compatibility
    public function location()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(
            Amenity::class,
            'property_amenity',
            'property_id',
            'amenity_id'
        );
    }

    public function inquiries()
    {
        return $this->hasMany(PropertyInquiry::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(
            User::class,
            'favorites',
            'property_id',
            'user_id'
        )->withTimestamps();
    }

    public function features()
    {
        return $this->hasOne(PropertyFeature::class);
    }

    public function getMainImageAttribute()
    {
        $img = $this->images()->orderByDesc('is_primary')->first();

        if (!$img) {
            return null;
        }

        return asset('storage/' . $img->image_path);
    }

    protected static function booted()
    {
        static::creating(function ($property) {
            if (empty($property->slug)) {
                $property->slug = Str::slug($property->title) . '-' . time();
            }
        });
    }

    public function getUrlAttribute()
{
    if (!$this->image_path) {
        return null;
    }

    return asset('storage/' . $this->image_path);
}
}