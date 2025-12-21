<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    /**
     * ✅ Relation: Wallet belongs to one User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ Relation: Wallet has many transactions
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * ✅ Add funds to the wallet
     */
    public function credit($amount, $type = 'credit', $description = null)
    {
        $this->balance += $amount;
        $this->save();

        $this->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'description' => $description ?? 'Funds added',
        ]);
    }

    /**
     * ✅ Deduct funds from the wallet
     */
    public function debit($amount, $type = 'debit', $description = null)
    {
        if ($this->balance >= $amount) {
            $this->balance -= $amount;
            $this->save();

            $this->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'description' => $description ?? 'Funds deducted',
            ]);
        } else {
            throw new \Exception('Insufficient balance in wallet');
        }
    }
}
