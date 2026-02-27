<x-app-layout>
    <x-slot name="header">Manage Users</x-slot>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.users') }}" class="mb-6 max-w-md">
        <div class="relative">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Reputation</th>
                    <th class="px-6 py-3">Joined</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                    <div class="flex gap-1 mt-0.5">
                                        @if($user->is_admin)
                                            <span class="text-xs bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded-full">Admin</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->reputation >= 5 ? 'bg-green-100 text-green-700' : ($user->reputation >= 0 ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700') }}">
                                {{ $user->reputation }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            @if($user->is_banned)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Banned</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @if($user->id !== Auth::id())
                                <div class="flex justify-end gap-2">
                                    @if(!$user->is_admin)
                                        <form method="POST" action="{{ route('admin.users.promote', $user) }}">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Promote {{ $user->name }} to admin?')"
                                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
                                                Promote
                                            </button>
                                        </form>
                                    @endif

                                    @if($user->is_banned)
                                        <form method="POST" action="{{ route('admin.users.unban', $user) }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-green-600 hover:text-green-800 font-medium transition">
                                                Unban
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Ban {{ $user->name }}?')"
                                                class="text-xs text-red-500 hover:text-red-700 font-medium transition">
                                                Ban
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <p class="text-xs text-gray-400 text-right">You</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="mt-6">{{ $users->withQueryString()->links() }}</div>
    @endif
</x-app-layout>
