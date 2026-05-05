<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoutiqueProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'currency',
        'type', 'category', 'badge', 'is_recommended',
        'requires_property', 'requires_published_property',
        'status', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_recommended' => 'boolean',
        'requires_property' => 'boolean',
        'requires_published_property' => 'boolean',
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