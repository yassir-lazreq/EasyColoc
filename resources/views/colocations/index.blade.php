<x-app-layout>
    <x-slot name="header">My Colocations</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('colocations.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition">
            + New Colocation
        </a>
    </x-slot>

    @if($colocations->isEmpty())
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">No colocation yet</h2>
            <p class="text-gray-500 mb-6">Create one or wait for an invitation.</p>
            <a href="{{ route('colocations.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                Create a colocation
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($colocations as $colocation)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col gap-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">{{ $colocation->name }}</h3>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $colocation->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($colocation->status) }}
                            </span>
                        </div>
                        @if($colocation->owner_id === Auth::id())
                            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full font-medium">Owner</span>
                        @endif
                    </div>

                    <div class="flex gap-6 text-sm text-gray-500">
                        <div>
                            <p class="font-semibold text-gray-800 text-lg">{{ $colocation->activeMembers->count() }}</p>
                            <p>Members</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-lg">&euro;{{ number_format($colocation->expenses->sum('amount'), 2) }}</p>
                            <p>Total expenses</p>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-auto">
                        <a href="{{ route('colocations.show', $colocation) }}" class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium py-2 rounded-xl text-sm transition">
                            View
                        </a>
                        @if($colocation->owner_id === Auth::id())
                            <a href="{{ route('colocations.edit', $colocation) }}" class="flex-1 text-center bg-gray-50 hover:bg-gray-100 text-gray-700 font-medium py-2 rounded-xl text-sm transition">
                                Edit
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($colocations->hasPages())
        <div class="mt-6">{{ $colocations->withQueryString()->links() }}</div>
    @endif
</x-app-layout>
