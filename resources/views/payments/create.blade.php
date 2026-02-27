<x-app-layout>
    <x-slot name="header">Record Payment &mdash; {{ $colocation->name }}</x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            <form method="POST" action="{{ route('colocations.payments.store', $colocation) }}">
                @csrf
                
                <div class="space-y-5">
                    {{-- From User --}}
                    <div>
                        <label for="from_user_id" class="block text-sm font-medium text-gray-700 mb-1">From (who paid)</label>
                        <select id="from_user_id" name="from_user_id" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('from_user_id') border-red-400 @enderror">
                            <option value="">— Select member —</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ old('from_user_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}{{ $member->id === Auth::id() ? ' (You)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('from_user_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- To User --}}
                    <div>
                        <label for="to_user_id" class="block text-sm font-medium text-gray-700 mb-1">To (who received)</label>
                        <select id="to_user_id" name="to_user_id" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('to_user_id') border-red-400 @enderror">
                            <option value="">— Select member —</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ old('to_user_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}{{ $member->id === Auth::id() ? ' (You)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('to_user_id')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (&euro;)</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('amount') border-red-400 @enderror">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div>
                        <label for="paid_at" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                        <input type="date" id="paid_at" name="paid_at" value="{{ old('paid_at', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none @error('paid_at') border-red-400 @enderror">
                        @error('paid_at')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl transition">
                        Record Payment
                    </button>
                    <a href="{{ route('colocations.payments.index', $colocation) }}" class="px-6 py-2.5 border border-gray-300 hover:bg-gray-50 rounded-xl text-gray-700 font-medium transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
