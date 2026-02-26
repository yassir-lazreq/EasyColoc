<section>
    <h2 class="text-base font-semibold text-gray-900 mb-1">Update Password</h2>
    <p class="text-sm text-gray-500 mb-6">Use a long, random password to keep your account secure.</p>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1.5">Current password</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            @foreach($errors->updatePassword->get('current_password') as $error)
                <p class="mt-1 text-xs text-red-500">{{ $error }}</p>
            @endforeach
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1.5">New password</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            @foreach($errors->updatePassword->get('password') as $error)
                <p class="mt-1 text-xs text-red-500">{{ $error }}</p>
            @endforeach
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm new password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            @foreach($errors->updatePassword->get('password_confirmation') as $error)
                <p class="mt-1 text-xs text-red-500">{{ $error }}</p>
            @endforeach
        </div>

        <div class="flex items-center gap-4 pt-1">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-xl transition text-sm">
                Update password
            </button>
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-medium">Saved!</p>
            @endif
        </div>
    </form>
</section>
