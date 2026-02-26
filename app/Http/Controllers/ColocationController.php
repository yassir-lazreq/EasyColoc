<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\User;
use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ColocationController extends Controller
{
    /**
     * Display a listing of the user's colocations.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get colocations where user is owner or member
        $colocations = Colocation::where(function ($query) use ($user) {
            $query->where('owner_id', $user->id)
                  ->orWhereHas('activeMembers', function ($q) use ($user) {
                      $q->where('user_id', $user->id);
                  });
        })
        ->with(['owner', 'activeMembers'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('colocations.index', compact('colocations'));
    }

    /**
     * Show the form for creating a new colocation.
     */
    public function create()
    {
        return view('colocations.create');
    }

    /**
     * Store a newly created colocation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        // A user can only be in one active colocation at a time
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if ($authUser->hasActiveColocation()) {
            return back()->withErrors(['error' => 'You are already a member of an active colocation. You must leave it before creating a new one.']);
        }

        DB::transaction(function () use ($validated) {
            $colocation = Colocation::create([
                'name' => $validated['name'],
                'owner_id' => Auth::id(),
                'status' => 'active',
            ]);

            // Automatically add owner as member
            $colocation->members()->attach(Auth::id(), [
                'role' => 'owner',
                'joined_at' => now(),
            ]);
        });

        return redirect()
            ->route('colocations.index')
            ->with('success', 'Colocation created successfully.');
    }

    /**
     * Display the specified colocation.
     */
    public function show(Colocation $colocation)
    {
        $this->ensureMemberAccess($colocation);

        $colocation->load(['owner', 'activeMembers', 'expenses.category', 'expenses.paidBy']);
        
        $recentExpenses = $colocation->expenses()
            ->with(['category', 'paidBy'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        return view('colocations.show', compact('colocation', 'recentExpenses'));
    }

    /**
     * Show the form for editing the specified colocation.
     */
    public function edit(Colocation $colocation)
    {
        // Only owner can edit
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Only the owner can edit this colocation.');
        }

        return view('colocations.edit', compact('colocation'));
    }

    /**
     * Update the specified colocation in storage.
     */
    public function update(Request $request, Colocation $colocation)
    {
        // Only owner can update
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Only the owner can update this colocation.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,cancelled'],
        ]);

        $colocation->update($validated);

        return redirect()
            ->route('colocations.show', $colocation)
            ->with('success', 'Colocation updated successfully.');
    }

    /**
     * Remove the specified colocation from storage.
     */
    public function destroy(Colocation $colocation)
    {
        // Only owner can delete
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Only the owner can delete this colocation.');
        }

        DB::transaction(function () use ($colocation) {
            $colocation->delete();
        });

        return redirect()
            ->route('colocations.index')
            ->with('success', 'Colocation deleted successfully.');
    }

    /**
     * Allow a user to leave a colocation.
     */
    public function leave(Colocation $colocation)
    {
        $user = Auth::user();

        // Owner cannot leave, must delete colocation or transfer ownership
        if ($colocation->owner_id === $user->id) {
            return back()->withErrors(['error' => 'Owner cannot leave. Please delete the colocation or transfer ownership first.']);
        }

        // Check if user is a member
        if (!$colocation->hasActiveMember($user)) {
            return back()->withErrors(['error' => 'You are not a member of this colocation.']);
        }

        DB::transaction(function () use ($colocation, $user) {
            // Update pivot table to mark as left
            $colocation->members()->updateExistingPivot($user->id, [
                'left_at' => now(),
            ]);
        });

        return redirect()
            ->route('colocations.index')
            ->with('success', 'You have left the colocation.');
    }

    /**
     * Remove a member from the colocation.
     */
    public function removeMember(Colocation $colocation, User $user)
    {
        // Only owner can remove members
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Only the owner can remove members.');
        }

        // Cannot remove owner
        if ($user->id === $colocation->owner_id) {
            return back()->withErrors(['error' => 'Cannot remove the owner.']);
        }

        // Check if user is a member
        if (!$colocation->hasActiveMember($user)) {
            return back()->withErrors(['error' => 'User is not a member of this colocation.']);
        }

        DB::transaction(function () use ($colocation, $user) {
            // Update pivot table to mark as left
            $colocation->members()->updateExistingPivot($user->id, [
                'left_at' => now(),
            ]);
        });

        return back()->with('success', 'Member removed successfully.');
    }

    /**
     * Display the balance sheet for the colocation.
     */
    public function balances(Colocation $colocation)
    {
        $this->ensureMemberAccess($colocation);

        // Get all balances for this colocation
        $balances = Balance::where('colocation_id', $colocation->id)
            ->with('user')
            ->get();

        // If no balances exist, create them for all members
        if ($balances->isEmpty()) {
            foreach ($colocation->activeMembers as $member) {
                Balance::create([
                    'colocation_id' => $colocation->id,
                    'user_id' => $member->id,
                    'total_paid' => 0,
                    'fair_share' => 0,
                    'balance' => 0,
                ]);
            }
            
            $balances = Balance::where('colocation_id', $colocation->id)
                ->with('user')
                ->get();
        }

        // Recalculate balances based on expenses
        $this->recalculateBalances($colocation);
        
        // Refresh balances after recalculation
        $balances = Balance::where('colocation_id', $colocation->id)
            ->with('user')
            ->get();

        // Separate who owes and who is owed
        $owedBalances = $balances->filter(fn($b) => $b->isOwed());
        $owingBalances = $balances->filter(fn($b) => $b->owes());
        $settledBalances = $balances->filter(fn($b) => $b->isSettled());

        return view('colocations.balances', compact('colocation', 'balances', 'owedBalances', 'owingBalances', 'settledBalances'));
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
