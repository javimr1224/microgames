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
        <div class="min-h-screen flex flex-col pt-12 sm:justify-start items-center sm:pt-12 bg-cover bg-center" style="background-image: url('{{ asset('images/lg-rg.png') }}');">
            <img class="w-[9rem]" src="{{asset('videos/trying-to-find-the-original-artist-behind-these-pixel-art-v0-26pmo44myiie1 1.gif')}}" alt="penguin">

            <div class="w-full sm:max-w-lg px-8 py-6 bg-white white:bg-white shadow-md overflow-hidden sm:rounded-lg">
                
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
