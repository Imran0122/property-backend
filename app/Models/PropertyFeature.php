<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyFeature extends Model
{
    protected $table = 'property_features'; // your table name
    protected $fillable = [
        'property_id',
        'bedrooms',
        'bathrooms',
        'area_size',
        'furnished',
        'floor',
        'parking',
    ];
}
