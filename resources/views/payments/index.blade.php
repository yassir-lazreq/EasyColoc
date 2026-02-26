<x-app-layout>
    <x-slot name="header">Payments &mdash; {{ $colocation->name }}</x-slot>
    <x-slot name="headerActions">
        <div class="flex gap-2">
            <a href="{{ route('colocations.show', $colocation) }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl text-sm transition">
                &larr; Back
            </a>
            <a href="{{ route('colocations.payments.create', $colocation) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition">
                + Record Payment
            </a>
        </div>
    </x-slot>

    @if($payments->isEmpty())
        <div class="text-center py-16">
            <p class="text-gray-400 mb-4">No payments recorded yet.</p>
            <a href="{{ route('colocations.payments.create', $colocation) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl transition text-sm">
                Record first payment
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-6 py-3">From</th>
                        <th class="px-6 py-3"></th>
                        <th class="px-6 py-3">To</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($payments as $payment)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $payment->fromUser->name }}</td>
                            <td class="px-6 py-3 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </td>
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $payment->toUser->name }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $payment->paid_at->format('d M Y') }}</td>
                            <td class="px-6 py-3 font-semibold text-green-600 text-right">&euro;{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-3">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('colocations.payments.show', [$colocation, $payment]) }}" class="text-gray-400 hover:text-indigo-600 transition">View</a>
                                    <a href="{{ route('colocations.payments.edit', [$colocation, $payment]) }}" class="text-gray-400 hover:text-gray-700 transition">Edit</a>
                                    <form method="POST" action="{{ route('colocations.payments.destroy', [$colocation, $payment]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this payment?')" class="text-gray-400 hover:text-red-500 transition">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($payments->hasPages())
        <div class="mt-6">{{ $payments->withQueryString()->links() }}</div>
    @endif
</x-app-layout>
