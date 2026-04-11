<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'city_id',
        'location',
        'developer',
        'description',
        'status',
        'cover_image',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'cover_image_url',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function units()
    {
        return $this->hasMany(ProjectUnit::class, 'project_id');
    }

    public function getCoverImageUrlAttribute()
    {
        if (!$this->cover_image) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $this->cover_image)) {
            return $this->cover_image;
        }

        return asset('storage/' . ltrim($this->cover_image, '/'));
    }
}