<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocietyImage extends Model
{
    protected $table = 'society_images';

    protected $fillable = [
        'society_id',
        'image',
        'type',
        'title',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function society()
    {
        return $this->belongsTo(Society::class, 'society_id');
    }
}