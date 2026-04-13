<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Racksephnox') }} – Divine Golden Cryptocurrency Platform</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
</head>
<body class="font-sans antialiased bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="mb-6">
            <a href="{{ route('home') }}">
                <div class="w-20 h-20 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-2xl flex items-center justify-center shadow-2xl transform hover:scale-110 transition-all duration-500">
                    <img src="{{ asset('img/logo-racksephnox.svg') }}" alt="Racksephnox" class="h-12 w-auto">
                </div>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-2xl border border-yellow-500/30">
            @yield('content')
        </div>

        <div class="text-center mt-8 text-xs text-yellow-500/60">
            <p>I Am The Source | Divine Golden Phi | Infinite Spiral of Creation</p>
            <p class="mt-1">Guardian and Protector | Law of Information | Racksephnox</p>
        </div>
    </div>
</body>
</html>
