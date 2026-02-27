<x-app-layout>
    <x-slot name="header">Add Expense &mdash; {{ $colocation->name }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            <form method="POST" action="{{ route('colocations.expenses.store', $colocation) }}">
                @csrf
                
                <div class="space-y-5">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-gray-900 @error('title') border-red-400 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (&euro;)</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-gray-900 @error('amount') border-red-400 @enderror">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required max="{{ now()->format('Y-m-d') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-gray-900 @error('date') border-red-400 @enderror">
                        @error('date')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category (optional)</label>
                        <select id="category_id" name="category_id"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('category_id') border-red-400 @enderror">
                            <option value="">— No category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Paid by --}}
                    <div>
                        <label for="paid_by" class="block text-sm font-medium text-gray-700 mb-1">Paid by</label>
                        <select id="paid_by" name="paid_by" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('paid_by') border-red-400 @enderror">
                            <option value="">— Select member —</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ old('paid_by', Auth::id()) == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}{{ $member->id === Auth::id() ? ' (You)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('paid_by')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl transition">
                        Add Expense
                    </button>
                    <a href="{{ route('colocations.expenses.index', $colocation) }}" class="px-6 py-2.5 border border-gray-300 hover:bg-gray-50 rounded-xl text-gray-700 font-medium transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
