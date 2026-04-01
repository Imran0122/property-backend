<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyPriceIndex extends Model
{
    protected $fillable = [
        'city_id',
        'area_id',
        'property_type_id',
        'purpose',
        'month_key',
        'avg_price',
        'avg_price_sqft',
        'listing_count',
    ];

    protected $casts = [
        'month_key' => 'date',
        'avg_price' => 'float',
        'avg_price_sqft' => 'float',
        'listing_count' => 'integer',
    ];
}