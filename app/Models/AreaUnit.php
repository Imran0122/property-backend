<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  // ✅ CORRECT
use Illuminate\Database\Eloquent\Model;

class AreaUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'conversion_to_sqft'
    ];
}