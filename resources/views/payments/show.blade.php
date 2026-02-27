<x-app-layout>
    <x-slot name="header">Payment Details</x-slot>
    <x-slot name="headerActions">
        <div class="flex gap-2">
            <a href="{{ route('colocations.payments.index', $colocation) }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl text-sm transition">
                &larr; Back
            </a>
            <a href="{{ route('colocations.payments.edit', [$colocation, $payment]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition">
                Edit
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            {{-- Payment Flow --}}
            <div class="flex items-center justify-around mb-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 text-red-700 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-2">
                        {{ strtoupper(substr($payment->fromUser->name, 0, 1)) }}
                    </div>
                    <p class="text-sm font-medium text-gray-800">{{ $payment->fromUser->name }}</p>
                    <p class="text-xs text-gray-400">Paid money</p>
                </div>

                <div class="flex-1 flex items-center justify-center px-6">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    <span class="text-2xl font-bold text-green-600">&euro;{{ number_format($payment->amount, 2) }}</span>
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-2">
                        {{ strtoupper(substr($payment->toUser->name, 0, 1)) }}
                    </div>
                    <p class="text-sm font-medium text-gray-800">{{ $payment->toUser->name }}</p>
                    <p class="text-xs text-gray-400">Received money</p>
                </div>
            </div>

            {{-- Details --}}
            <dl class="space-y-4 border-t border-gray-100 pt-6">
                <div>
                    <dt class="text-sm text-gray-500">Payment Date</dt>
                    <dd class="font-medium text-gray-800">{{ $payment->paid_at->format('d F Y') }}</dd>
                </div>
            </dl>
        </div>

        {{-- Delete action --}}
        <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Danger Zone</h3>
            <p class="text-sm text-gray-500 mb-4">Deleting this payment will reverse its effect on member balances.</p>
            <div class="flex justify-between items-center">
                <form method="POST" action="{{ route('colocations.payments.destroy', [$colocation, $payment]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this payment?')" class="text-sm text-red-500 hover:text-red-700 transition">
                        Delete this payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
