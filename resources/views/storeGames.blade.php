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

            <div class="mt-16 max-w-7xl mx-auto grid gap-8 lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1">
                @foreach ($games as $game)
                    <div
                        class="relative group cursor-pointer transition-all duration-300 hover:scale-105 border-2 border-gray-600 bg-gray-900/80 backdrop-blur-sm hover:border-white rounded-lg overflow-hidden shadow-lg">
                        <div class="p-6">
                            <div class="relative z-10">
                                <img class="w-full h-40 object-cover mb-4 rounded-lg"
                                    src="{{ asset($game->image) }}" alt="{{ $game->name }}">
                                <h3 class="text-2xl tracking-wider text-white mb-2 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-cyan-400 group-hover:to-pink-400 group-hover:bg-clip-text transition-all duration-300"
                                    style="font-family: 'Press Start 2P', monospace;">
                                    {{ $game->name }}
                                </h3>
                                <p class="text-gray-300 text-sm mb-4" style="font-family: 'Press Start 2P', monospace;">
                                    {{ $game->description }}
                                </p>
                                <a href="http://localhost:3000"
                                    class="w-full inline-block text-center tracking-wider bg-gradient-to-r from-green-500 to-emerald-600 hover:shadow-lg hover:shadow-current/50 transition-all duration-300 border-0 py-2 px-4 rounded-md"
                                    style="font-family: 'Press Start 2P', monospace;">
                                    JUGAR
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </main>

        <x-footer />
    </div>
</body>

</html>
