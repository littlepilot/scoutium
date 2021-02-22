<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'balance',
        'currency',
        'default',
    ];

    protected $casts = [
        'balance' => 'float',
        'default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function lastTenTransactions()
    {
        return $this->transactions()
            ->orderByDesc(static::CREATED_AT)
            ->limit(10);
    }

    public function createTransaction(float $amount, string $message, string $type = 'deposit')
    {
        $this->transactions()->create([
            'amount' => $amount,
            'message' => $message,
            'type' => $type,
        ]);
    }

    public function deposit(float $amount, string $message)
    {
        $this->createTransaction($amount, $message);
        $this->balance += $amount;
        $this->save();
    }

    public function withdraw(float $amount, string $message)
    {
        if ($this->balance < $amount) {
            throw new \InvalidArgumentException('Amount cannot be greater than balance');
        }

        $this->createTransaction($amount, $message, 'withdraw');
        $this->balance -= $amount;
        $this->save();
    }

    public function reward(float $amount, string $message)
    {
        $this->createTransaction($amount, $message, 'reward');
        $this->balance += $amount;
        $this->save();
    }
}
