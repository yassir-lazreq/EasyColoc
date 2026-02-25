<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'colocation_id',
        'title',
        'amount',
        'date',
        'category_id',
        'paid_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the colocation that owns the expense.
     */
    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    /**
     * Get the user who paid the expense.
     */
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Get the category of the expense.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to filter expenses by month.
     */
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('date', $month)
                     ->whereYear('date', $year);
    }

    /**
     * Scope a query to filter expenses by year.
     */
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('date', $year);
    }

    /**
     * Scope a query to order by date descending.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('date', 'desc');
    }
}
