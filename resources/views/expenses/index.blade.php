<x-app-layout>
    <x-slot name="header">Expenses &mdash; {{ $colocation->name }}</x-slot>
    <x-slot name="headerActions">
        <div class="flex gap-2">
            <a href="{{ route('colocations.show', $colocation) }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl text-sm transition">
                &larr; Back
            </a>
            <a href="{{ route('colocations.expenses.create', $colocation) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition">
                + Add Expense
            </a>
        </div>
    </x-slot>

    {{-- Summary bar --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Total</p>
            <p class="text-2xl font-bold text-indigo-600">&euro;{{ number_format($totalExpenses, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Your share</p>
            <p class="text-2xl font-bold text-gray-800">&euro;{{ number_format($totalExpenses / max($colocation->activeMembers->count(), 1), 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Expenses count</p>
            <p class="text-2xl font-bold text-gray-800">{{ $expenses->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-500 mb-1">Colocation</p>
            <p class="text-lg font-bold text-gray-700 truncate">{{ $colocation->name }}</p>
        </div>
    </div>

    @if($expenses->isEmpty())
        <div class="text-center py-16">
            <p class="text-gray-400 mb-4">No expenses recorded yet.</p>
            <a href="{{ route('colocations.expenses.create', $colocation) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl transition text-sm">
                Add the first expense
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Paid by</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $expense->title }}</td>
                            <td class="px-6 py-3">
                                @if($expense->category)
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full text-xs">{{ $expense->category->name }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $expense->paidBy->name }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $expense->date->format('d M Y') }}</td>
                            <td class="px-6 py-3 font-semibold text-gray-800 text-right">&euro;{{ number_format($expense->amount, 2) }}</td>
                            <td class="px-6 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('colocations.expenses.show', [$colocation, $expense]) }}" class="text-gray-400 hover:text-indigo-600 transition">View</a>
                                    <a href="{{ route('colocations.expenses.edit', [$colocation, $expense]) }}" class="text-gray-400 hover:text-gray-700 transition">Edit</a>
                                    <form method="POST" action="{{ route('colocations.expenses.destroy', [$colocation, $expense]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this expense?')" class="text-gray-400 hover:text-red-500 transition">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($expenses->hasPages())
        <div class="mt-6">{{ $expenses->withQueryString()->links() }}</div>
    @endif
</x-app-layout>
