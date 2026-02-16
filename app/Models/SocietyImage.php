<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocietyImage extends Model
{
    use HasFactory;

    protected $table = 'society_images';

    protected $fillable = [
        'society_id',
        'image'
    ];

    /**
     * Relationship: Image belongs to a Society
     */
    public function society()
    {
        return $this->belongsTo(Society::class, 'society_id');
    }
}
