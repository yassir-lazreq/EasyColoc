<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation &mdash; EasyColoc</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-900">EasyColoc</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">You're invited!</h1>
            <p class="text-gray-500 mb-1">You've been invited to join</p>
            <p class="text-xl font-bold text-indigo-700 mb-6">{{ $invitation->colocation->name }}</p>

            <div class="bg-gray-50 rounded-xl p-4 mb-6 text-sm text-left">
                <p class="text-gray-600"><span class="font-medium">Owner:</span> {{ $invitation->colocation->owner->name }}</p>
                <p class="text-gray-600 mt-1"><span class="font-medium">Members:</span> {{ $invitation->colocation->members->count() }} member(s)</p>
            </div>

            @if(!Auth::check())
                <p class="text-sm text-amber-600 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 mb-6">
                    Please <a href="{{ route('login') }}" class="font-semibold underline">sign in</a> or <a href="{{ route('register') }}" class="font-semibold underline">create an account</a> to accept this invitation.
                </p>
            @endif

            <div class="flex gap-3">
                <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl transition">
                        Decline
                    </button>
                </form>
                <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl transition">
                        Accept invitation
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
