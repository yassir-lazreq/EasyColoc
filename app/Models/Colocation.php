<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colocation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'owner_id',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the owner of the colocation.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all members of the colocation.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'colocation_user')
            ->withPivot('role', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    /**
     * Get only active members (left_at is null).
     */
    public function activeMembers()
    {
        return $this->belongsToMany(User::class, 'colocation_user')
            ->wherePivot('left_at', null)
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get the expenses for the colocation.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the balances for the colocation.
     */
    public function balances()
    {
        return $this->hasMany(Balance::class);
    }

    /**
     * Get the payments for the colocation.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the invitations for the colocation.
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Get pending invitations.
     */
    public function pendingInvitations()
    {
        return $this->hasMany(Invitation::class)
            ->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    /**
     * Check if the colocation is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the colocation is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Cancel the colocation.
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Check if a user is the owner.
     */
    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    /**
     * Check if a user is a member.
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a user is an active member.
     */
    public function hasActiveMember(User $user): bool
    {
        return $this->activeMembers()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the total number of active members.
     */
    public function getActiveMembersCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }

    /**
     * Get the total expenses amount.
     */
    public function getTotalExpensesAttribute(): float
    {
        return $this->expenses()->sum('amount');
    }
}
