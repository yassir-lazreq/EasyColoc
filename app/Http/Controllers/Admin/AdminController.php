<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Colocation;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with stats.
     */
    public function index()
    {
        $stats = [
            'total_users'       => User::count(),
            'total_colocations' => Colocation::count(),
            'banned_users'      => User::where('is_banned', true)->count(),
            'active_colocations'=> Colocation::where('status', 'active')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * List all users.
     */
    public function users(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * Ban a user.
     */
    public function ban(User $user)
    {
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Cannot ban an admin.']);
        }

        $user->ban();

        return back()->with('success', "User {$user->name} has been banned.");
    }

    /**
     * Unban a user.
     */
    public function unban(User $user)
    {
        $user->unban();

        return back()->with('success', "User {$user->name} has been unbanned.");
    }

    /**
     * Promote a user to admin.
     */
    public function promote(User $user)
    {
        $user->promoteToAdmin();

        return back()->with('success', "{$user->name} has been promoted to admin.");
    }

    /**
     * List all colocations.
     */
    public function colocations()
    {
        $colocations = Colocation::with(['owner', 'activeMembers'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.colocations', compact('colocations'));
    }

    /**
     * Delete a colocation.
     */
    public function destroyColocation(Colocation $colocation)
    {
        $colocation->delete();

        return back()->with('success', 'Colocation deleted successfully.');
    }
}
