<x-app-layout>
    <x-slot name="header">Expense Details</x-slot>
    <x-slot name="headerActions">
        <div class="flex gap-2">
            <a href="{{ route('colocations.expenses.index', $colocation) }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl text-sm transition">
                &larr; Back
            </a>
            <a href="{{ route('colocations.expenses.edit', [$colocation, $expense]) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition">
                Edit
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            {{-- Header --}}
            <div class="border-b border-gray-100 pb-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900">{{ $expense->title }}</h2>
                @if($expense->category)
                    <span class="inline-block mt-2 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-sm">{{ $expense->category->name }}</span>
                @endif
            </div>

            <div class="text-4xl font-bold text-indigo-600">&euro;{{ number_format($expense->amount, 2) }}</div>

            {{-- Details --}}
            <dl class="mt-6 space-y-4">
                <div>
                    <dt class="text-sm text-gray-500">Paid by</dt>
                    <dd class="font-medium text-gray-800">{{ $expense->paidBy->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Date</dt>
                    <dd class="font-medium text-gray-800">{{ $expense->date->format('d F Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Share per member</dt>
                    <dd class="font-medium text-gray-800">&euro;{{ number_format($expense->amount / max($colocation->members->count(), 1), 2) }}</dd>
                </div>
            </dl>
        </div>

        {{-- Delete action --}}
        <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Danger Zone</h3>
            <p class="text-sm text-gray-500 mb-4">Deleting this expense will recalculate all member balances.</p>
            <div class="flex justify-between items-center">
                <form method="POST" action="{{ route('colocations.expenses.destroy', [$colocation, $expense]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this expense?')" class="text-sm text-red-500 hover:text-red-700 transition">
                        Delete this expense
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
