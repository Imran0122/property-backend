<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'logo', 'phone', 'email', 'address', 'description'
    ];

    // public function agents()
    // {
    //     return $this->hasMany(Agent::class);
    // }
        public function agents()
{
    return $this->hasMany(User::class);
}

    public function properties()
    {
        return $this->hasManyThrough(Property::class, Agent::class);
    }


}
