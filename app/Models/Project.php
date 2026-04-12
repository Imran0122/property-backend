<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'cover_image_url',
    ];

    protected static function booted()
    {
        static::creating(function ($project) {
            if (empty($project->slug) && !empty($project->title)) {
                $project->slug = Str::slug($project->title) . '-' . time();
            }
        });
    }

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

    $path = str_replace('\\', '/', trim($this->cover_image));

    if (Str::startsWith($path, ['http://', 'https://'])) {
        return preg_replace('/^http:\/\//i', 'https://', $path);
    }

    $clean = ltrim($path, '/');

    if (Str::startsWith($clean, 'storage/')) {
        return url($clean);
    }

    return url('storage/' . $clean);
}
}