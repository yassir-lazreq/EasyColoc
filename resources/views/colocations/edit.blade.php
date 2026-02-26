<x-app-layout>
    <x-slot name="header">Edit Colocation</x-slot>

    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            <form method="POST" action="{{ route('colocations.update', $colocation) }}" class="space-y-6">
                @csrf @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Colocation name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $colocation->name) }}" required autofocus
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-gray-900 @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-gray-900">
                        <option value="active" @selected(old('status', $colocation->status) === 'active')>Active</option>
                        <option value="archived" @selected(old('status', $colocation->status) === 'archived')>Archived</option>
                    </select>
                </div>

                <hr class="border-gray-100">

                <div>
                    <h4 class="text-sm font-semibold text-red-600 mb-3">Danger Zone</h4>
                    <form method="POST" action="{{ route('colocations.destroy', $colocation) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this colocation? This cannot be undone.')"
                            class="text-sm text-red-500 hover:text-red-700 underline transition">
                            Delete colocation
                        </button>
                    </form>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('colocations.show', $colocation) }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl transition">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl transition">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
