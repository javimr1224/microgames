<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $game->name }} - Microgames</title>
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

        <main class="max-w-7xl mx-auto mt-12 py-12 px-4">
            <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">
                <div class="md:w-1/2">
                    <img src="{{ $game->image_url }}" alt="{{ $game->name }}" class="rounded-lg shadow-lg w-full">
                </div>
                <div class="md:w-1/2 flex flex-col justify-between">
                    <div>
                        <h1 class="text-4xl font-bold mb-4" style="font-family: 'Press Start 2P', cursive;">
                            {{ $game->name }}</h1>
                        <p class="text-lg text-gray-300 mb-4">{{ $game->description }}</p>
                        <p class="text-xl font-semibold mb-4">Categoría: {{ $game->category }}</p>
                        @if (Auth::check() && in_array((string) $game->id, $purchasedGameIds))
                            <p class="text-2xl font-bold mb-6">¡Comprado!</p>
                            <a href="{{ route('game.play', $game) }}"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-xl"
                                style="font-family: 'Press Start 2P', cursive;">
                                JUGAR
                            </a>
                        @elseif($game->price)
                            <p class="text-2xl font-bold mb-6">Precio: {{ number_format($game->price, 2) }}€</p>
                            <form action="{{ route('cart.add', $game) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-xl"
                                    style="font-family: 'Press Start 2P', cursive;">
                                    Añadir al Carrito
                                </button>
                            </form>
                        @else
                            <p class="text-2xl font-bold mb-6">Gratis</p>
                            <a href="{{ route('game.play', $game) }}"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-xl"
                                style="font-family: 'Press Start 2P', cursive;">
                                Jugar Ahora
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    <x-footer />
</body>

</html>
