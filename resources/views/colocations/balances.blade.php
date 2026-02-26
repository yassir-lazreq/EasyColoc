<x-app-layout>
    <x-slot name="header">Balance Sheet &mdash; {{ $colocation->name }}</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('colocations.show', $colocation) }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl text-sm transition">
            &larr; Back
        </a>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
        {{-- Individual Balances --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Member Balances</h3>
            <div class="space-y-3">
                @foreach($balances as $balance)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($balance->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $balance->user->name }}</p>
                                @if($balance->user_id === Auth::id())
                                    <p class="text-xs text-indigo-400">You</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold {{ $balance->balance >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                {{ $balance->balance >= 0 ? '+' : '' }}&euro;{{ number_format(abs($balance->balance), 2) }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $balance->balance > 0 ? 'Gets back' : ($balance->balance < 0 ? 'Owes' : 'Even') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Who owes whom --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Settlement Summary</h3>
            @if($owingBalances->isEmpty())
                <div class="text-center py-8">
                    <p class="text-4xl mb-2">🎉</p>
                    <p class="text-gray-500 text-sm">Everyone is even!</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($owingBalances as $owing)
                        @foreach($owedBalances as $owed)
                            <div class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                                <span class="text-sm font-medium text-red-600">{{ $owing->user->name }}</span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                                <span class="text-sm font-medium text-green-600">{{ $owed->user->name }}</span>
                                <span class="ml-auto text-sm font-semibold text-gray-800">&euro;{{ number_format(abs($owing->balance), 2) }}</span>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @endif

            <div class="mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('colocations.payments.create', $colocation) }}" class="block text-center bg-green-50 hover:bg-green-100 text-green-700 font-medium py-2.5 rounded-xl text-sm transition">
                    Record a payment
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
