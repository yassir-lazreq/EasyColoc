<x-app-layout>
    <x-slot name="header">Admin Dashboard</x-slot>

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Total Users</p>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Banned Users</p>
            <p class="text-3xl font-bold text-red-500">{{ $stats['banned_users'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Colocations</p>
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total_colocations'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Active Colocations</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['active_colocations'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Quick links --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Manage</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.users') }}" class="flex items-center justify-between p-3 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                    <span class="font-medium">Users</span>
                    <span class="bg-indigo-200 text-indigo-800 text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats['total_users'] }}</span>
                </a>
                <a href="{{ route('admin.colocations') }}" class="flex items-center justify-between p-3 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 transition">
                    <span class="font-medium">Colocations</span>
                    <span class="bg-purple-200 text-purple-800 text-xs font-bold px-2 py-0.5 rounded-full">{{ $stats['total_colocations'] }}</span>
                </a>
            </div>
        </div>

        {{-- Recent registrations --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Recent Registrations</h3>
            <div class="space-y-2">
                @foreach($recentUsers as $user)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                        @if($user->is_banned)
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Banned</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
