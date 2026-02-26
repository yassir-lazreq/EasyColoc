<x-app-layout>
    <x-slot name="header">Record a Payment</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('colocations.payments.index', $colocation) }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl text-sm transition">
            &larr; Back
        </a>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            <form method="POST" action="{{ route('colocations.payments.store', $colocation) }}" class="space-y-5">
                @csrf

                <div>
                    <label for="from_user_id" class="block text-sm font-medium text-gray-700 mb-1.5">From (who paid)</label>
                    <select id="from_user_id" name="from_user_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('from_user_id') border-red-400 @enderror">
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" @selected(old('from_user_id', Auth::id()) == $member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                    @error('from_user_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="to_user_id" class="block text-sm font-medium text-gray-700 mb-1.5">To (who receives)</label>
                    <select id="to_user_id" name="to_user_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('to_user_id') border-red-400 @enderror">
                        <option value="">— Select member —</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" @selected(old('to_user_id') == $member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                    @error('to_user_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">Amount (&euro;)</label>
                        <input id="amount" name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount') }}" required
                            placeholder="0.00"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('amount') border-red-400 @enderror">
                        @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="paid_at" class="block text-sm font-medium text-gray-700 mb-1.5">Date</label>
                        <input id="paid_at" name="paid_at" type="date" value="{{ old('paid_at', date('Y-m-d')) }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('paid_at') border-red-400 @enderror">
                        @error('paid_at') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('colocations.payments.index', $colocation) }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl transition">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl transition">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
