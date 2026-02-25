<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Payment;
use App\Models\Balance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments for a colocation.
     */
    public function index(Colocation $colocation)
    {
        $this->ensureMemberAccess($colocation);

        $payments = $colocation->payments()
            ->with(['fromUser', 'toUser'])
            ->orderBy('paid_at', 'desc')
            ->paginate(20);

        return view('payments.index', compact('colocation', 'payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Colocation $colocation)
    {
        $this->ensureMemberAccess($colocation);

        $members = $colocation->activeMembers;

        return view('payments.create', compact('colocation', 'members'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request, Colocation $colocation)
    {
        $this->ensureMemberAccess($colocation);

        $validated = $request->validate([
            'from_user_id' => ['required', 'exists:users,id'],
            'to_user_id' => ['required', 'exists:users,id', 'different:from_user_id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'paid_at' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        // Verify both users are members of this colocation
        $memberIds = $colocation->activeMembers->pluck('id')->toArray();
        
        if (!in_array($validated['from_user_id'], $memberIds) || 
            !in_array($validated['to_user_id'], $memberIds)) {
            return back()->withErrors(['error' => 'Both users must be members of this colocation.']);
        }

        DB::transaction(function () use ($colocation, $validated) {
            // Create the payment
            $payment = $colocation->payments()->create([
                'from_user_id' => $validated['from_user_id'],
                'to_user_id' => $validated['to_user_id'],
                'amount' => $validated['amount'],
                'paid_at' => $validated['paid_at'] ?? now(),
            ]);

            // Update balances: from_user paid money, so their balance increases
            // to_user received money, so their balance decreases
            $this->updateBalancesAfterPayment($colocation, $payment);
        });

        return redirect()
            ->route('colocations.payments.index', $colocation)
            ->with('success', 'Payment recorded successfully.');
    }

    /**
     * Display the specified payment.
     */
    public function show(Colocation $colocation, Payment $payment)
    {
        $this->ensureMemberAccess($colocation);

        // Ensure payment belongs to this colocation
        if ($payment->colocation_id !== $colocation->id) {
            abort(404);
        }

        $payment->load(['fromUser', 'toUser']);

        return view('payments.show', compact('colocation', 'payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Colocation $colocation, Payment $payment)
    {
        $this->ensureMemberAccess($colocation);

        // Ensure payment belongs to this colocation
        if ($payment->colocation_id !== $colocation->id) {
            abort(404);
        }

        $members = $colocation->activeMembers;

        return view('payments.edit', compact('colocation', 'payment', 'members'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, Colocation $colocation, Payment $payment)
    {
        $this->ensureMemberAccess($colocation);

        // Ensure payment belongs to this colocation
        if ($payment->colocation_id !== $colocation->id) {
            abort(404);
        }

        $validated = $request->validate([
            'from_user_id' => ['required', 'exists:users,id'],
            'to_user_id' => ['required', 'exists:users,id', 'different:from_user_id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'paid_at' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        // Verify both users are members of this colocation
        $memberIds = $colocation->activeMembers->pluck('id')->toArray();
        
        if (!in_array($validated['from_user_id'], $memberIds) || 
            !in_array($validated['to_user_id'], $memberIds)) {
            return back()->withErrors(['error' => 'Both users must be members of this colocation.']);
        }

        DB::transaction(function () use ($colocation, $payment, $validated) {
            // Reverse the old payment's effect on balances
            $this->reverseBalancesBeforeUpdate($colocation, $payment);

            // Update the payment
            $payment->update([
                'from_user_id' => $validated['from_user_id'],
                'to_user_id' => $validated['to_user_id'],
                'amount' => $validated['amount'],
                'paid_at' => $validated['paid_at'] ?? $payment->paid_at,
            ]);

            // Apply the new payment's effect on balances
            $this->updateBalancesAfterPayment($colocation, $payment);
        });

        return redirect()
            ->route('colocations.payments.index', $colocation)
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Colocation $colocation, Payment $payment)
    {
        $this->ensureMemberAccess($colocation);

        // Ensure payment belongs to this colocation
        if ($payment->colocation_id !== $colocation->id) {
            abort(404);
        }

        DB::transaction(function () use ($colocation, $payment) {
            // Reverse the payment's effect on balances
            $this->reverseBalancesBeforeUpdate($colocation, $payment);

            // Delete the payment
            $payment->delete();
        });

        return redirect()
            ->route('colocations.payments.index', $colocation)
            ->with('success', 'Payment deleted successfully.');
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

    /**
     * Update balances after a payment is created.
     */
    protected function updateBalancesAfterPayment(Colocation $colocation, Payment $payment)
    {
        // From user paid money to to_user
        // This reduces from_user's debt (or increases what they're owed)
        // And increases to_user's debt (or reduces what they're owed)

        $fromBalance = Balance::firstOrCreate(
            ['colocation_id' => $colocation->id, 'user_id' => $payment->from_user_id],
            ['total_paid' => 0, 'fair_share' => 0, 'balance' => 0]
        );

        $toBalance = Balance::firstOrCreate(
            ['colocation_id' => $colocation->id, 'user_id' => $payment->to_user_id],
            ['total_paid' => 0, 'fair_share' => 0, 'balance' => 0]
        );

        // Update balances: payment reduces debt
        $fromBalance->balance += $payment->amount;
        $fromBalance->save();

        $toBalance->balance -= $payment->amount;
        $toBalance->save();
    }

    /**
     * Reverse balance changes before updating or deleting a payment.
     */
    protected function reverseBalancesBeforeUpdate(Colocation $colocation, Payment $payment)
    {
        $fromBalance = Balance::where('colocation_id', $colocation->id)
            ->where('user_id', $payment->from_user_id)
            ->first();

        $toBalance = Balance::where('colocation_id', $colocation->id)
            ->where('user_id', $payment->to_user_id)
            ->first();

        if ($fromBalance) {
            $fromBalance->balance -= $payment->amount;
            $fromBalance->save();
        }

        if ($toBalance) {
            $toBalance->balance += $payment->amount;
            $toBalance->save();
        }
    }
}
