<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    @php $colocation = Auth::check() ? Auth::user()->activeColocation() : null; @endphp

    @if($colocation)
        {{-- Has a colocation --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <p class="text-sm text-gray-500 mb-1">Colocation</p>
                <p class="text-xl font-bold text-gray-900">{{ $colocation->name }}</p>
                <p class="text-sm text-gray-400 mt-1">{{ $colocation->activeMembers()->count() }} member(s)</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <p class="text-sm text-gray-500 mb-1">Total Expenses</p>
                <p class="text-3xl font-bold text-indigo-600">&euro;{{ number_format($colocation->expenses->sum('amount'), 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                @php
                    $balance = $colocation->balances()->where('user_id', Auth::id())->first();
                @endphp
                <p class="text-sm text-gray-500 mb-1">Your Balance</p>
                @if($balance)
                    <p class="text-3xl font-bold {{ $balance->balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $balance->balance >= 0 ? '+' : '' }}&euro;{{ number_format(abs($balance->balance), 2) }}
                    </p>
                    <p class="text-sm text-gray-400 mt-1">{{ $balance->balance >= 0 ? 'You are owed money' : 'You owe money' }}</p>
                @else
                    <p class="text-3xl font-bold text-gray-400">&euro;0.00</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Quick Actions --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('colocations.expenses.create', $colocation) }}" class="flex items-center gap-3 p-3 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add an expense
                    </a>
                    <a href="{{ route('colocations.payments.create', $colocation) }}" class="flex items-center gap-3 p-3 rounded-xl bg-green-50 hover:bg-green-100 text-green-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Record a payment
                    </a>
                    <a href="{{ route('colocations.balances', $colocation) }}" class="flex items-center gap-3 p-3 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        View balance sheet
                    </a>
                    <a href="{{ route('colocations.show', $colocation) }}" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Manage colocation
                    </a>
                </div>
            </div>

            {{-- Recent Expenses --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Recent Expenses</h3>
                    <a href="{{ route('colocations.expenses.index', $colocation) }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                </div>
                @php $recentExpenses = $colocation->expenses()->with(['category','paidBy'])->orderBy('date','desc')->limit(5)->get(); @endphp
                @forelse($recentExpenses as $expense)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $expense->title }}</p>
                            <p class="text-xs text-gray-400">{{ $expense->paidBy->name }} &bull; {{ $expense->date->format('d M') }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-700">&euro;{{ number_format($expense->amount, 2) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">No expenses yet.</p>
                @endforelse
            </div>
        </div>
    @else
        {{-- No colocation --}}
        <div class="max-w-lg mx-auto text-center py-16">
            <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">You're not in a colocation yet</h2>
            <p class="text-gray-500 mb-8">Create one or wait for an invitation from a roommate.</p>
            <a href="{{ route('colocations.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                Create a colocation
            </a>
        </div>
    @endif
</x-app-layout>
