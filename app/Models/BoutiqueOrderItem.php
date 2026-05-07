<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoutiqueOrderItem extends Model
{
    protected $table = 'boutique_order_items';

    protected $fillable = [
        'boutique_order_id',
        'boutique_product_id',
        'title',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'currency',
    ];

    public function product()
    {
        return $this->belongsTo(BoutiqueProduct::class, 'boutique_product_id');
    }

    public function order()
    {
        return $this->belongsTo(BoutiqueOrder::class, 'boutique_order_id');
    }
}