<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'reputation',
        'is_admin',
        'is_banned',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_banned' => 'boolean',
        ];
    }

    /**
     * Get the colocations owned by the user.
     */
    public function ownedColocations()
    {
        return $this->hasMany(Colocation::class, 'owner_id');
    }

    /**
     * Get all colocations the user is a member of (including owned).
     */
    public function colocations()
    {
        return $this->belongsToMany(Colocation::class, 'colocation_user')
            ->withPivot('role', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    /**
     * Get the active colocation for the user.
     */
    public function activeColocation()
    {
        return $this->belongsToMany(Colocation::class, 'colocation_user')
            ->wherePivot('left_at', null)
            ->where('status', 'active')
            ->withPivot('role', 'joined_at')
            ->first();
    }

    /**
     * Check if user has an active colocation.
     */
    public function hasActiveColocation(): bool
    {
        return $this->colocations()
            ->wherePivot('left_at', null)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get the expenses paid by the user.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'paid_by');
    }

    /**
     * Get the balances for the user.
     */
    public function balances()
    {
        return $this->hasMany(Balance::class);
    }

    /**
     * Get payments made by the user.
     */
    public function paymentsMade()
    {
        return $this->hasMany(Payment::class, 'from_user_id');
    }

    /**
     * Get payments received by the user.
     */
    public function paymentsReceived()
    {
        return $this->hasMany(Payment::class, 'to_user_id');
    }

    /**
     * Get invitations sent by the user.
     */
    public function sentInvitations()
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    /**
     * Increment user reputation.
     */
    public function incrementReputation(): void
    {
        $this->increment('reputation');
    }

    /**
     * Decrement user reputation.
     */
    public function decrementReputation(): void
    {
        $this->decrement('reputation');
    }

    /**
     * Ban the user.
     */
    public function ban(): void
    {
        $this->update(['is_banned' => true]);
    }

    /**
     * Unban the user.
     */
    public function unban(): void
    {
        $this->update(['is_banned' => false]);
    }

    /**
     * Check if user is banned.
     */
    public function isBanned(): bool
    {
        return $this->is_banned;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Promote user to admin.
     */
    public function promoteToAdmin(): void
    {
        $this->update(['is_admin' => true]);
    }
}