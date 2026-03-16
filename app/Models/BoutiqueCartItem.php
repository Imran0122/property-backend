<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoutiqueCartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'boutique_product_id',
        'property_id',
        'quantity',
        'unit_price',
        'currency',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(BoutiqueProduct::class, 'boutique_product_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}