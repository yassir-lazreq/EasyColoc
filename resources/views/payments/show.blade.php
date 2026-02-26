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

    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 space-y-6">
            {{-- Transfer visual --}}
            <div class="flex items-center justify-between gap-4">
                <div class="text-center flex-1">
                    <div class="w-14 h-14 rounded-full bg-red-100 text-red-700 flex items-center justify-center text-xl font-bold mx-auto mb-2">
                        {{ strtoupper(substr($payment->fromUser->name, 0, 1)) }}
                    </div>
                    <p class="text-sm font-medium text-gray-800">{{ $payment->fromUser->name }}</p>
                    <p class="text-xs text-gray-400">Paid</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    <span class="text-2xl font-bold text-green-600">&euro;{{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="text-center flex-1">
                    <div class="w-14 h-14 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xl font-bold mx-auto mb-2">
                        {{ strtoupper(substr($payment->toUser->name, 0, 1)) }}
                    </div>
                    <p class="text-sm font-medium text-gray-800">{{ $payment->toUser->name }}</p>
                    <p class="text-xs text-gray-400">Received</p>
                </div>
            </div>

            <dl class="divide-y divide-gray-50">
                <div class="py-3 flex justify-between text-sm">
                    <dt class="text-gray-500">Date</dt>
                    <dd class="font-medium text-gray-800">{{ $payment->paid_at->format('d F Y') }}</dd>
                </div>
                <div class="py-3 flex justify-between text-sm">
                    <dt class="text-gray-500">Colocation</dt>
                    <dd class="font-medium">
                        <a href="{{ route('colocations.show', $colocation) }}" class="text-indigo-600 hover:underline">{{ $colocation->name }}</a>
                    </dd>
                </div>
            </dl>

            <div>
                <form method="POST" action="{{ route('colocations.payments.destroy', [$colocation, $payment]) }}">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this payment?')" class="text-sm text-red-500 hover:text-red-700 transition">
                        Delete this payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
