<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PropertyType extends Model
{
    protected $fillable = ['name'];

    public function getSlugAttribute()
    {
        return Str::slug($this->name);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
