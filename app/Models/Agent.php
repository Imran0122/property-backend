<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'phone', 'company', 'license_number', 'bio'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function properties() {
        return $this->hasMany(Property::class);
    }
    public function agency()
{
    return $this->belongsTo(Agency::class);
}
}
