<x-app-layout>
    <x-slot name="header">Profile</x-slot>

    <div class="max-w-2xl space-y-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
