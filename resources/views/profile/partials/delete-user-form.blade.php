<section x-data="{ confirmDelete: false }">
    <h2 class="text-base font-semibold text-red-600 mb-1">Delete Account</h2>
    <p class="text-sm text-gray-500 mb-6">Once your account is deleted, all data will be permanently removed. This action cannot be undone.</p>

    <button type="button" @click="confirmDelete = true"
        class="bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2.5 px-6 rounded-xl border border-red-200 transition text-sm">
        Delete my account
    </button>

    {{-- Confirmation modal --}}
    <div x-show="confirmDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="absolute inset-0 bg-black/40" @click="confirmDelete = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100 w-full max-w-md p-8 z-10">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Are you sure?</h3>
            <p class="text-sm text-gray-500 mb-6">Enter your password to confirm you want to permanently delete your account.</p>

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                @csrf
                @method('delete')

                <div>
                    <label for="del_password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input id="del_password" name="password" type="password" placeholder="Your current password" autofocus
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none @if($errors->userDeletion->get('password')) border-red-400 @endif">
                    @foreach($errors->userDeletion->get('password') as $error)
                        <p class="mt-1 text-xs text-red-500">{{ $error }}</p>
                    @endforeach
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="confirmDelete = false"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl transition text-sm">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                        Delete account
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
