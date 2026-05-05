<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

  protected $fillable = [
    'user_id',
    'name',
    'logo',
    'phone',
    'email',
    'address',
    'description',
    'slug',
    'status',
    'city',
    'website',
];

    public function agents()
    {
        return $this->hasMany(Agent::class, 'agency_id');
    }

    public function properties()
    {
        return $this->hasManyThrough(
            Property::class,
            Agent::class,
            'agency_id', // agents.agency_id -> agencies.id
            'user_id',   // properties.user_id -> agents.user_id
            'id',        // agencies.id
            'user_id'    // agents.user_id
        );
    }
}