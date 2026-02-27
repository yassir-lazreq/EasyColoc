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

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Total Expenses</p>
            <p class="text-2xl font-bold text-indigo-600">&euro;{{ number_format($totalExpenses, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Total Count</p>
            <p class="text-2xl font-bold text-gray-800">{{ $expenses->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500">Per member</p>
            <p class="text-2xl font-bold text-gray-800">&euro;{{ number_format($totalExpenses / max($colocation->activeMembers->count(), 1), 2) }}</p>
        </div>
    </div>

    {{-- Expense List --}}
    @if($expenses->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <p class="text-gray-400 mb-4">No expenses yet.</p>
            <a href="{{ route('colocations.expenses.create', $colocation) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl transition text-sm">
                Add first expense
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Paid by</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $expense->title }}</td>
                            <td class="px-6 py-3">
                                @if($expense->category)
                                    <span class="inline-block px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full text-xs">
                                        {{ $expense->category->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-600 text-sm">{{ $expense->paidBy->name }}</td>
                            <td class="px-6 py-3 text-gray-500 text-sm">{{ $expense->date->format('d M Y') }}</td>
                            <td class="px-6 py-3 font-semibold text-indigo-600 text-right">&euro;{{ number_format($expense->amount, 2) }}</td>
                            <td class="px-6 py-3 text-right text-sm">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('colocations.expenses.show', [$colocation, $expense]) }}" class="text-gray-400 hover:text-indigo-600 transition">View</a>
                                    <a href="{{ route('colocations.expenses.edit', [$colocation, $expense]) }}" class="text-gray-400 hover:text-gray-700 transition">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $expenses->links() }}
        </div>
    @endif
</x-app-layout>
