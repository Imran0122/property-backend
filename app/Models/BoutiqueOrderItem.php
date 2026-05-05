<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoutiqueOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'boutique_order_id', 'boutique_product_id',
        'title', 'description', 'quantity',
        'unit_price', 'total_price', 'currency',
        'type', 'category',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(BoutiqueOrder::class, 'boutique_order_id');
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