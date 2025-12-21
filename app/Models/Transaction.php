<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','package_id','gateway','gateway_id','amount','currency','status','payload'
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function package() {
        return $this->belongsTo(Package::class);
    }

    public function isSuccessful() {
        return $this->status === 'succeeded';
    }
}
