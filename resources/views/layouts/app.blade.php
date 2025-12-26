<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased">
    @php
        $currentRoute = request()->route()->getName();
        $hideLayout = in_array($currentRoute, ['quiz.login', 'quiz.page', 'quiz.start']);
    @endphp

    <div class="min-h-screen bg-gray-100">
        @unless ($hideLayout)
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset
        @endunless

        <!-- Page Content -->
        <main class="{{ $hideLayout ? 'flex items-center justify-center min-h-screen' : '' }}">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts

    @unless ($hideLayout)
        @include('layouts.footer')
    @endunless
</body>

</html>
