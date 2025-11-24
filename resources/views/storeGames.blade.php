<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Store - Microgames</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="antialiased bg-gray-900 text-white font-sans flex flex-col min-h-screen">
    <div class="flex-grow">
        <x-header />

        <main class="py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto text-center">
                <h1 style="font-family: 'Press Start 2P', cursive; font-size: 50px;">
                    Game Store
                </h1>
                <p class="mt-6 max-w-3xl mx-auto text-gray-300" style="font-size: 20px;">
                    Browse our collection of exciting games!
                </p>
            </div>
        </main>

        <x-footer />
    </div>
</body>

</html>
