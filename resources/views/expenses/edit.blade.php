<x-app-layout>
    <x-slot name="header">Edit Expense</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('colocations.expenses.show', [$colocation, $expense]) }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl text-sm transition">
            &larr; Back
        </a>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            <form method="POST" action="{{ route('colocations.expenses.update', [$colocation, $expense]) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $expense->title) }}" required autofocus
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('title') border-red-400 @enderror">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">Amount (&euro;)</label>
                        <input id="amount" name="amount" type="number" step="0.01" min="0" value="{{ old('amount', $expense->amount) }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('amount') border-red-400 @enderror">
                        @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1.5">Date</label>
                        <input id="date" name="date" type="date" value="{{ old('date', $expense->date->format('Y-m-d')) }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('date') border-red-400 @enderror">
                        @error('date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1.5">Category</label>
                    <select id="category_id" name="category_id"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">— No category —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $expense->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="paid_by" class="block text-sm font-medium text-gray-700 mb-1.5">Paid by</label>
                    <select id="paid_by" name="paid_by" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('paid_by') border-red-400 @enderror">
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" @selected(old('paid_by', $expense->paid_by) == $member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                    @error('paid_by') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('colocations.expenses.show', [$colocation, $expense]) }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl transition">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
