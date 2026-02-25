<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'colocation_id',
        'from_user_id',
        'to_user_id',
        'amount',
        'paid_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the colocation that owns the payment.
     */
    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    /**
     * Get the user who made the payment.
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the user who received the payment.
     */
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * Scope a query to get payments for a specific colocation.
     */
    public function scopeForColocation($query, $colocationId)
    {
        return $query->where('colocation_id', $colocationId);
    }

    /**
     * Scope a query to order by payment date descending.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('paid_at', 'desc');
    }
}
