<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoutiqueCartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'boutique_product_id',
        'quantity', 'unit_price', 'total_price', 'currency',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
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