<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome Page Placeholder</h1>
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Figtree:wght@400;600;700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative flex min-h-screen flex-col items-center justify-center bg-gray-100 dark:bg-gray-900 selection:bg-red-500 selection:text-white">

            {{-- الزرين في الأعلى --}}
            @if (Route::has('login'))
                <div class="fixed top-0 right-0 p-6 text-right z-10">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            {{-- المحتوى الرئيسي --}}
            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <div class="text-center">
                    <h1 class="text-5xl font-bold text-gray-800 dark:text-white mb-4">Welcome to Laravel</h1>
                    <p class="text-gray-500 dark:text-gray-400">
                        Build something amazing with Laravel Breeze.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
