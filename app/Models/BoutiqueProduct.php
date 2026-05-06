<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoutiqueProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'category',
        'type',
        'icon',
        'badge',
        'is_active',
        'is_recommended',
        'sort_order',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'is_active'      => 'boolean',
        'is_recommended' => 'boolean',
    ];

    public function cartItems()
    {
        return $this->hasMany(BoutiqueCartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(BoutiqueOrderItem::class);
    }
}