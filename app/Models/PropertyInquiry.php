<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyInquiry extends Model
{
    protected $table = 'property_inquiries';

    protected $fillable = [
        'property_id',
        'name',
        'email',
        'phone',
        'message',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}