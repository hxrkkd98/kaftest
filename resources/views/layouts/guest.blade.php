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
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#bebbb4]">
            <div class="mb-4">
                <a href="/">
                    <img src="{{ asset('images/logo.png') }}" alt="KAF Logo" class="w-[300px]">
                </a>
            </div>
            <p class="text-[20px] font-bold text-[#5a1f24]">KAF IT-VCM</p>
            <p class="text-[20px] font-bold text-[#5a1f24]">VENDOR CONTROL MONITOR</p>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg  bg-[#DBD5CE]">
                {{ $slot }}
            </div>
        </div>
        
    </body>
</html>
