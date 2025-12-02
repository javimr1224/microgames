<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mis Juegos - Microgames</title>
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
                    Mis Juegos
                </h1>
                <p class="mt-6 max-w-3xl mx-auto text-gray-300" style="font-size: 20px;">
                    Aquí tienes todos los juegos que has adquirido. ¡A disfrutar!
                </p>
            </div>

            <div id="games-container" class="mt-16 max-w-7xl mx-auto grid gap-8 lg:grid-cols-4 md:grid-cols-2 sm:grid-cols-1">
                @forelse ($purchasedGames as $game)
                    <div
                        class="relative group game-card cursor-pointer transition-all duration-300 hover:scale-105 border-2 border-gray-600 bg-gray-900/80 backdrop-blur-sm hover:border-white rounded-lg overflow-hidden shadow-lg">
                        <div class="p-6">
                            <div class="relative z-10">
                                <a href="{{ route('game.launch', $game) }}">
                                    <img class="w-full h-40 object-cover mb-4 rounded-lg game-card-image"
                                        src="{{ asset($game->image) }}" alt="{{ $game->name }}">
                                    <img src="{{ asset('videos/may-sitting-near-waterfall-pokemon-emerald-pixel-wallpaperwaifu-com-ezgif.com-video-to-gif-converter.gif') }}" alt="Game GIF"
                                        class="w-full h-40 object-cover mb-4 rounded-lg game-card-video hidden">
                                </a>
                                <h3 class="text-2xl tracking-wider text-white mb-2 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-cyan-400 group-hover:to-pink-400 group-hover:bg-clip-text transition-all duration-300"
                                    style="font-family: 'Press Start 2P', monospace;">
                                    {{ $game->name }}
                                </h3>
                                <p class="text-gray-300 text-sm mb-4" style="font-family: 'Press Start 2P', monospace;">
                                    {{ $game->description }}
                                </p>
                                <a href="{{ route('game.launch', $game) }}"
                                    class="w-full inline-block text-center tracking-wider bg-gradient-to-r from-green-500 to-emerald-600 hover:shadow-lg hover:shadow-current/50 transition-all duration-300 border-0 py-2 px-4 rounded-md"
                                    style="font-family: 'Press Start 2P', monospace;">
                                    JUGAR
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-4 md:col-span-2 sm:col-span-1 text-center bg-gray-800 p-12 rounded-lg">
                        <p class="text-2xl text-gray-400 mb-6">Aún no has comprado ningún juego.</p>
                        <a href="{{ route('storeGames') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg" style="font-family: 'Press Start 2P', cursive;">
                            Ir a la tienda
                        </a>
                    </div>
                @endforelse
            </div>
        </main>

        <x-footer />
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('games-container');
            if (container) {
                const cards = container.querySelectorAll('.game-card');
                cards.forEach(card => {
                    const image = card.querySelector('.game-card-image');
                    const video = card.querySelector('.game-card-video');

                    if (image && video) {
                        const hoverTarget = card.querySelector('a:first-of-type');
                        if (hoverTarget) {
                            hoverTarget.addEventListener('mouseenter', () => {
                                image.classList.add('hidden');
                                video.classList.remove('hidden');
                            });

                            hoverTarget.addEventListener('mouseleave', () => {
                                video.classList.add('hidden');
                                image.classList.remove('hidden');
                            });
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>
