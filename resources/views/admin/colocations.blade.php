<x-app-layout>
    <x-slot name="header">Manage Colocations</x-slot>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Owner</th>
                    <th class="px-6 py-3">Members</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Created</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($colocations as $colocation)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $colocation->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $colocation->owner->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $colocation->activeMembers->count() }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colocation->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($colocation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $colocation->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-right">
                            <form method="POST" action="{{ route('admin.colocations.destroy', $colocation) }}">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete colocation {{ $colocation->name }}? This cannot be undone.')"
                                    class="text-xs text-red-500 hover:text-red-700 font-medium transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-sm">No colocations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($colocations->hasPages())
        <div class="mt-6">{{ $colocations->links() }}</div>
    @endif
</x-app-layout>
