<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'image',
        'excerpt',
        'description',
        'content',
        'category',
        'author',
        'writer',
        'reading_time',
        'status',
    ];
}