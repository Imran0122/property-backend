<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'construction_type_id',
        'construction_mode_id',
        'rate_per_sqft'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function constructionType()
    {
        return $this->belongsTo(ConstructionType::class);
    }

    public function constructionMode()
    {
        return $this->belongsTo(ConstructionMode::class);
    }
}