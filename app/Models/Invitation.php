<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'colocation_id',
        'email',
        'token',
        'status',
        'invited_by',
        'expires_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the colocation that owns the invitation.
     */
    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    /**
     * Get the user who sent the invitation.
     */
    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Generate a unique token for the invitation.
     */
    public static function generateToken(): string
    {
        return Str::random(32);
    }

    /**
     * Check if the invitation is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the invitation is accepted.
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Check if the invitation is refused.
     */
    public function isRefused(): bool
    {
        return $this->status === 'refused';
    }

    /**
     * Check if the invitation is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the invitation is valid.
     */
    public function isValid(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }

    /**
     * Accept the invitation.
     */
    public function accept(): void
    {
        $this->update(['status' => 'accepted']);
    }

    /**
     * Refuse the invitation.
     */
    public function refuse(): void
    {
        $this->update(['status' => 'refused']);
    }

    /**
     * Scope a query to only include pending invitations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include valid invitations.
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'pending')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope a query to find by token.
     */
    public function scopeByToken($query, $token)
    {
        return $query->where('token', $token);
    }
}
