<?php

namespace App\Models;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['city_id', 'name'];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    protected static function boot()
{
    parent::boot();

    static::creating(function ($area) {
        $area->slug = Str::slug($area->name);
    });
}
}
