<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'description',
    ];

    /**
     * ✅ Relation: Transaction belongs to one wallet
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * ✅ Accessor for formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'PKR ' . number_format($this->amount, 2);
    }

    /**
     * ✅ Accessor for formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d M Y, h:i A');
    }
}
