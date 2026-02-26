<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wrong Account &mdash; EasyColoc</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md text-center">
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

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-10">
            <div class="text-5xl mb-4">🔒</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-3">Wrong account</h1>
            <p class="text-gray-500 mb-2">This invitation was sent to a different email address.</p>
            @if(isset($invitation))
                <p class="text-sm bg-amber-50 text-amber-700 border border-amber-100 rounded-xl px-4 py-3 mb-6">
                    The invitation was sent to <strong>{{ $invitation->email }}</strong>
                </p>
            @else
                <div class="mb-6"></div>
            @endif
            <div class="flex flex-col gap-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl transition">
                        Log out &amp; sign in with the right account
                    </button>
                </form>
                <a href="{{ route('dashboard') }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl transition">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>
