<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // 👈 YE ADD KARO

class ConstructionMode extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];
}