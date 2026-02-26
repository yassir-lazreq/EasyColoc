<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses for a colocation.
     */
    public function index(Colocation $colocation)
    {
        $this->ensureMemberAccess($colocation);

        $expenses = $colocation->expenses()
            ->with(['category', 'paidBy'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        $totalExpenses = $colocation->expenses->sum('amount');
        $categories = $colocation->categories()->orderBy('name')->get();

        return view('expenses.index', compact('colocation', 'expenses', 'totalExpenses', 'categories'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create(Colocation $colocation)
    {
        $this->ensureMemberAccess($colocation);

        $members = $colocation->activeMembers;
        $categories = $colocation->categories()->orderBy('name')->get();

        return view('expenses.create', compact('colocation', 'members', 'categories'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request, Colocation $colocation)
    {
        $this->ensureMemberAccess($colocation);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'category_id' => ['nullable', 'exists:categories,id,colocation_id,' . $colocation->id],
            'paid_by' => ['required', 'exists:users,id'],
        ]);

        // Verify paid_by user is a member of this colocation
        if (!$colocation->hasActiveMember(User::find($validated['paid_by']))) {
            return back()->withErrors(['paid_by' => 'The selected user is not a member of this colocation.']);
        }

        DB::transaction(function () use ($colocation, $validated) {
            // Create the expense
            $expense = $colocation->expenses()->create($validated);

            // Recalculate balances
            $this->recalculateBalances($colocation);
        });

        return redirect()
            ->route('colocations.expenses.index', $colocation)
            ->with('success', 'Expense added successfully.');
    }

    /**
     * Display the specified expense.
     */
    public function show(Colocation $colocation, Expense $expense)
    {
        $this->ensureMemberAccess($colocation);

        // Ensure expense belongs to this colocation
        if ($expense->colocation_id !== $colocation->id) {
            abort(404);
        }

        $expense->load(['category', 'paidBy']);

        return view('expenses.show', compact('colocation', 'expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Colocation $colocation, Expense $expense)
    {
        $this->ensureMemberAccess($colocation);

        // Ensure expense belongs to this colocation
        if ($expense->colocation_id !== $colocation->id) {
            abort(404);
        }

        $members = $colocation->activeMembers;
        $categories = $colocation->categories()->orderBy('name')->get();

        return view('expenses.edit', compact('colocation', 'expense', 'members', 'categories'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Colocation $colocation, Expense $expense)
    {
        $this->ensureMemberAccess($colocation);

        // Ensure expense belongs to this colocation
        if ($expense->colocation_id !== $colocation->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'category_id' => ['nullable', 'exists:categories,id,colocation_id,' . $colocation->id],
            'paid_by' => ['required', 'exists:users,id'],
        ]);

        // Verify paid_by user is a member of this colocation
        $paidByUser = \App\Models\User::find($validated['paid_by']);
        if (!$colocation->hasActiveMember($paidByUser)) {
            return back()->withErrors(['paid_by' => 'The selected user is not a member of this colocation.']);
        }

        DB::transaction(function () use ($colocation, $expense, $validated) {
            // Update the expense
            $expense->update($validated);

            // Recalculate balances
            $this->recalculateBalances($colocation);
        });

        return redirect()
            ->route('colocations.expenses.index', $colocation)
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Colocation $colocation, Expense $expense)
    {
        $this->ensureMemberAccess($colocation);

        // Ensure expense belongs to this colocation
        if ($expense->colocation_id !== $colocation->id) {
            abort(404);
        }

        DB::transaction(function () use ($colocation, $expense) {
            // Delete the expense
            $expense->delete();

            // Recalculate balances
            $this->recalculateBalances($colocation);
        });

        return redirect()
            ->route('colocations.expenses.index', $colocation)
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * Recalculate balances for a colocation based on expenses.
     */
    protected function recalculateBalances(Colocation $colocation)
    {
        $members = $colocation->activeMembers;
        $memberCount = $members->count();

        if ($memberCount === 0) {
            return;
        }

        // Get all expenses for this colocation
        $expenses = $colocation->expenses;
        $totalExpenses = $expenses->sum('amount');
        $fairShare = $totalExpenses / $memberCount;

        // Calculate how much each member has paid
        foreach ($members as $member) {
            $totalPaid = $expenses->where('paid_by', $member->id)->sum('amount');
            
            $balance = Balance::firstOrCreate(
                ['colocation_id' => $colocation->id, 'user_id' => $member->id],
                ['total_paid' => 0, 'fair_share' => 0, 'balance' => 0]
            );

            $balance->update([
                'total_paid' => $totalPaid,
                'fair_share' => $fairShare,
                'balance' => $totalPaid - $fairShare,
            ]);
        }
    }

    /**
     * Ensure the current user is a member of the colocation.
     */
    protected function ensureMemberAccess(Colocation $colocation)
    {
        if (!$colocation->hasActiveMember(Auth::user())) {
            abort(403, 'You are not a member of this colocation.');
        }
    }
}
