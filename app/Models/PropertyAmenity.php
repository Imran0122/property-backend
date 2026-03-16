<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PropertyAmenity extends Model
{
    protected $table = 'property_amenity'; // 👈 YE LINE ADD KARO

    protected $fillable = [
        'property_id',
        'amenity_id'
    ];
}