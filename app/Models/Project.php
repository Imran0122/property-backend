<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    protected $appends = ['cover_image_url'];

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
        $value = $this->cover_image;

        if (!$value) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        if (Str::startsWith($value, ['/storage/', 'storage/'])) {
            return url('/' . ltrim($value, '/'));
        }

        return Storage::disk('public')->url($value);
    }
}