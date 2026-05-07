<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoutiqueOrder extends Model
{
    protected $table = 'boutique_orders';

    protected $fillable = [
        'user_id',
        'order_number',
        'total',
        'currency',
        'payment_method',
        'payment_proof',
        'status',
        'payment_status',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(BoutiqueOrderItem::class, 'boutique_order_id');
    }
}