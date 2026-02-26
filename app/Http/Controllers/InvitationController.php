<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvitationController extends Controller
{
    /**
     * Store a newly created invitation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'colocation_id' => ['required', 'exists:colocations,id'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $colocation = Colocation::findOrFail($validated['colocation_id']);

        // Only owner or members can send invitations
        if (!$colocation->hasActiveMember(Auth::user())) {
            abort(403, 'You are not authorized to send invitations for this colocation.');
        }

        // Check if user with this email already exists and is a member
        $existingUser = User::where('email', $validated['email'])->first();
        if ($existingUser && $colocation->hasActiveMember($existingUser)) {
            return back()->withErrors(['email' => 'This user is already a member of the colocation.']);
        }

        // Check if there's already a pending invitation for this email
        $existingInvitation = Invitation::where('colocation_id', $colocation->id)
            ->where('email', $validated['email'])
            ->valid()
            ->first();

        if ($existingInvitation) {
            return back()->withErrors(['email' => 'There is already a pending invitation for this email.']);
        }

        // Create the invitation
        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => $validated['email'],
            'token' => Invitation::generateToken(),
            'status' => 'pending',
            'invited_by' => Auth::id(),
            'expires_at' => now()->addDays(7),
        ]);

        // TODO: Send email notification with invitation link
        // Mail::to($validated['email'])->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation sent successfully.');
    }

    /**
     * Remove the specified invitation from storage (cancel invitation).
     */
    public function destroy(Invitation $invitation)
    {
        $colocation = $invitation->colocation;

        // Only the person who sent the invitation or the colocation owner can cancel it
        if ($invitation->invited_by !== Auth::id() && $colocation->owner_id !== Auth::id()) {
            abort(403, 'You are not authorized to cancel this invitation.');
        }

        $invitation->delete();

        return back()->with('success', 'Invitation cancelled successfully.');
    }

    /**
     * Show the invitation accept/refuse page (public).
     */
    public function showAccept($token)
    {
        $invitation = Invitation::byToken($token)->with(['colocation', 'invitedBy'])->firstOrFail();

        // Check if invitation is still valid
        if (!$invitation->isValid()) {
            return view('invitations.expired', compact('invitation'));
        }

        // Check if user is already logged in
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if email matches
            if ($user->email !== $invitation->email) {
                return view('invitations.wrong-user', compact('invitation'));
            }

            // Check if already a member
            if ($invitation->colocation->hasActiveMember($user)) {
                return redirect()
                    ->route('colocations.show', $invitation->colocation)
                    ->with('info', 'You are already a member of this colocation.');
            }
        }

        return view('invitations.accept', compact('invitation'));
    }

    /**
     * Accept the invitation.
     */
    public function accept($token)
    {
        $invitation = Invitation::byToken($token)->with('colocation')->firstOrFail();

        // Check if invitation is still valid
        if (!$invitation->isValid()) {
            return back()->withErrors(['error' => 'This invitation has expired or is no longer valid.']);
        }

        // If user is not logged in, redirect to register/login
        if (!Auth::check()) {
            session(['pending_invitation_token' => $token]);
            return redirect()->route('register')->with('info', 'Please register or login to accept this invitation.');
        }

        $user = Auth::user();

        // Check if email matches
        if ($user->email !== $invitation->email) {
            return back()->withErrors(['error' => 'This invitation was sent to a different email address.']);
        }

        // Check if already a member of this colocation
        if ($invitation->colocation->hasActiveMember($user)) {
            return redirect()
                ->route('colocations.show', $invitation->colocation)
                ->with('info', 'You are already a member of this colocation.');
        }

        // A user can only be in one active colocation at a time
        /** @var \App\Models\User $user */
        if ($user->hasActiveColocation()) {
            return back()->withErrors(['error' => 'You are already a member of an active colocation. You must leave it before joining a new one.']);
        }

        DB::transaction(function () use ($invitation, $user) {
            // Add user to colocation
            $invitation->colocation->members()->attach($user->id, [
                'role' => 'member',
                'joined_at' => now(),
            ]);

            // Mark invitation as accepted
            $invitation->accept();

            // Create balance record for the new member
            \App\Models\Balance::create([
                'colocation_id' => $invitation->colocation_id,
                'user_id' => $user->id,
                'total_paid' => 0,
                'fair_share' => 0,
                'balance' => 0,
            ]);
        });

        return redirect()
            ->route('colocations.show', $invitation->colocation)
            ->with('success', 'You have successfully joined the colocation!');
    }

    /**
     * Refuse the invitation.
     */
    public function refuse($token)
    {
        $invitation = Invitation::byToken($token)->firstOrFail();

        // Check if invitation is still valid
        if (!$invitation->isValid()) {
            return back()->withErrors(['error' => 'This invitation has expired or is no longer valid.']);
        }

        DB::transaction(function () use ($invitation) {
            // Mark invitation as refused
            $invitation->refuse();
        });

        return redirect()
            ->route('dashboard')
            ->with('info', 'You have declined the invitation.');
    }
}
