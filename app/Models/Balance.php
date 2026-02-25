<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'colocation_id',
        'user_id',
        'total_paid',
        'fair_share',
        'balance',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_paid' => 'decimal:2',
            'fair_share' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    /**
     * Get the colocation that owns the balance.
     */
    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    /**
     * Get the user that owns the balance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the user owes money.
     */
    public function owes(): bool
    {
        return $this->balance < 0;
    }

    /**
     * Check if the user is owed money.
     */
    public function isOwed(): bool
    {
        return $this->balance > 0;
    }

    /**
     * Check if the balance is settled.
     */
    public function isSettled(): bool
    {
        return abs((float)$this->balance) < 0.01; // Consider settled if less than 1 cent
    }

    /**
     * Get the absolute balance amount.
     */
    public function getAbsoluteBalanceAttribute(): float
    {
        return abs((float)$this->balance);
    }
}
