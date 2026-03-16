<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoutiqueProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'type',
        'description',
        'price',
        'currency',
        'duration_days',
        'badge',
        'is_recommended',
        'requires_property',
        'requires_published_property',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_recommended' => 'boolean',
        'requires_property' => 'boolean',
        'requires_published_property' => 'boolean',
        'is_active' => 'boolean',
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