<x-app-layout>
    <x-slot name="header">{{ $colocation->name }}</x-slot>
    <x-slot name="headerActions">
        <div class="flex gap-2">
            <a href="{{ route('colocations.expenses.index', $colocation) }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl text-sm transition">
                Expenses
            </a>
            <a href="{{ route('colocations.balances', $colocation) }}" class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl text-sm transition">
                Balances
            </a>
            @if($colocation->owner_id === Auth::id())
                <a href="{{ route('colocations.edit', $colocation) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition">
                    Edit
                </a>
            @endif
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Members --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Members ({{ $colocation->activeMembers->count() }})</h3>
                <ul class="space-y-3">
                    @foreach($colocation->activeMembers as $member)
                        <li class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $member->name }}</p>
                                    @if($member->id === $colocation->owner_id)
                                        <p class="text-xs text-indigo-500">Owner</p>
                                    @endif
                                </div>
                            </div>
                            @if($colocation->owner_id === Auth::id() && $member->id !== Auth::id())
                                <form method="POST" action="{{ route('colocations.members.remove', [$colocation, $member]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Remove {{ $member->name }}?')" class="text-xs text-red-500 hover:text-red-700 transition">
                                        Remove
                                    </button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>

                @if($colocation->owner_id === Auth::id())
                    <hr class="my-4 border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Invite someone</h4>
                    <form method="POST" action="{{ route('invitations.store') }}" class="space-y-2">
                        @csrf
                        <input type="hidden" name="colocation_id" value="{{ $colocation->id }}">
                        <input type="email" name="email" placeholder="Email address" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-xl text-sm transition">
                            Send invitation
                        </button>
                    </form>

                    {{-- Pending invitations with copy-link --}}
                    @if($pendingInvitations->isNotEmpty())
                        <div class="mt-4 space-y-2">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Pending invitations</h4>
                            @foreach($pendingInvitations as $inv)
                                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3"
                                    x-data="{ copied: false }">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-800 truncate">{{ $inv->email }}</span>
                                        <div class="flex items-center gap-2 ml-2 shrink-0">
                                            <span class="text-xs text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">
                                                expires {{ $inv->expires_at->diffForHumans() }}
                                            </span>
                                            <form method="POST" action="{{ route('invitations.destroy', $inv) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 transition">✕</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" readonly
                                            value="{{ route('invitations.show-accept', $inv->token) }}"
                                            class="flex-1 px-3 py-1.5 text-xs rounded-lg border border-gray-200 bg-white text-gray-600 outline-none select-all min-w-0">
                                        <button type="button"
                                            @click="navigator.clipboard.writeText('{{ route('invitations.show-accept', $inv->token) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                            :class="copied ? 'bg-green-100 text-green-700' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200'">
                                            <span x-show="!copied">Copy</span>
                                            <span x-show="copied">Copied!</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif

                @if($colocation->owner_id !== Auth::id())
                    <hr class="my-4 border-gray-100">
                    <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Leave this colocation?')" class="w-full text-center text-sm text-red-500 hover:text-red-700 transition font-medium">
                            Leave colocation
                        </button>
                    </form>
                @endif
            </div>

            {{-- Categories (owner only) --}}
            @if($colocation->owner_id === Auth::id())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6" x-data="{ editingId: null, editName: '', editDesc: '' }">
            <h3 class="font-semibold text-gray-800 mb-4">Categories</h3>

            {{-- Existing categories --}}
            <ul class="space-y-2 mb-4">
                @forelse($colocation->categories()->orderBy('name')->get() as $cat)
                    <li>
                        {{-- View mode --}}
                        <div x-show="editingId !== {{ $cat->id }}" class="flex items-center justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $cat->name }}</p>
                                @if($cat->description)
                                    <p class="text-xs text-gray-400">{{ $cat->description }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button"
                                    @click="editingId = {{ $cat->id }}; editName = '{{ addslashes($cat->name) }}'; editDesc = '{{ addslashes($cat->description ?? '') }}'"
                                    class="text-xs text-indigo-500 hover:text-indigo-700 transition">Edit</button>
                                <form method="POST" action="{{ route('colocations.categories.destroy', [$colocation, $cat]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Delete category \'{{ $cat->name }}\'? Expenses using it will become uncategorised.')"
                                        class="text-xs text-red-400 hover:text-red-600 transition">Delete</button>
                                </form>
                            </div>
                        </div>
                        {{-- Edit mode --}}
                        <form x-show="editingId === {{ $cat->id }}"
                            method="POST" action="{{ route('colocations.categories.update', [$colocation, $cat]) }}"
                            class="space-y-1">
                            @csrf @method('PUT')
                            <input type="text" name="name" x-model="editName" required
                                class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                            <input type="text" name="description" x-model="editDesc" placeholder="Description (optional)"
                                class="w-full px-3 py-1.5 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="px-3 py-1 text-xs rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">Save</button>
                                <button type="button" @click="editingId = null"
                                    class="px-3 py-1 text-xs rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Cancel</button>
                            </div>
                        </form>
                    </li>
                @empty
                    <li class="text-sm text-gray-400 italic">No categories yet.</li>
                @endforelse
            </ul>

            {{-- Add new category --}}
            <hr class="my-3 border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Add category</p>
            <form method="POST" action="{{ route('colocations.categories.store', $colocation) }}" class="space-y-2">
                @csrf
                <input type="text" name="name" placeholder="Name (e.g. Groceries)" required
                    class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                <input type="text" name="description" placeholder="Description (optional)"
                    class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 outline-none">
                <button type="submit"
                    class="w-full bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold py-2 rounded-xl text-sm transition">
                    + Add category
                </button>
            </form>
            </div>
            @endif
        </div>

        {{-- Recent Activity --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <p class="text-sm text-gray-500">Total expenses</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">&euro;{{ number_format($colocation->expenses->sum('amount'), 2) }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <p class="text-sm text-gray-500">Expenses this month</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">&euro;{{ number_format($colocation->expenses->where('date', '>=', now()->startOfMonth())->sum('amount'), 2) }}</p>
                </div>
            </div>

            {{-- Recent Expenses --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Recent Expenses</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('colocations.expenses.create', $colocation) }}" class="text-sm text-indigo-600 hover:underline">+ Add</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('colocations.expenses.index', $colocation) }}" class="text-sm text-gray-500 hover:underline">View all</a>
                    </div>
                </div>
                @php $recent = $colocation->expenses()->with(['category','paidBy'])->orderBy('date','desc')->limit(6)->get(); @endphp
                @forelse($recent as $expense)
                    <div class="flex items-center justify-between px-6 py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                                <span class="text-xs font-bold text-indigo-600">{{ strtoupper(substr($expense->category->name ?? 'G', 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $expense->title }}</p>
                                <p class="text-xs text-gray-400">{{ $expense->paidBy->name }} &bull; {{ $expense->date->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800">&euro;{{ number_format($expense->amount, 2) }}</p>
                            @if($expense->category)
                                <span class="text-xs text-gray-400">{{ $expense->category->name }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 text-sm">No expenses yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
