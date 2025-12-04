<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrito - Microgames</title>
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

        <main class="max-w-4xl mx-auto mt-12 py-12 px-4">
            <h1 style="font-family: 'Press Start 2P';" class="text-4xl text-white text-center mb-12">Tu Carrito</h1>

            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($total > 0)
                <div class="bg-gray-800 shadow-md rounded-lg p-6">
                    @foreach($games as $game)
                        <div class="flex items-center justify-between py-4 border-b border-gray-700">
                            <div class="flex items-center">
                                <img src="{{ asset($game->image) }}" alt="{{ $game->name }}" class="w-24 h-24 object-cover rounded-md mr-6">
                                <div>
                                    <h2 class="text-xl font-bold" style="font-family: 'Press Start 2P', cursive;">{{ $game->name }}</h2>
                                    <p class="text-gray-400">{{ number_format($game->price, 2) }}€</p>
                                </div>
                            </div>
                            <form action="{{ route('cart.remove', $game->id) }}" method="POST">
                                @csrf
                                <button style="font-family: 'Press Start 2P', cursive;" type="submit" class="text-red-500 hover:text-red-700 font-semibold">Eliminar</button>
                            </form>
                        </div>
                    @endforeach

                    <div class="mt-8 text-right">
                        <p class="text-xl font-bold">Total: <span class="text-sm" style="font-family: 'Press Start 2P', cursive;">{{ number_format($total, 2) }}€</span></p>
                        <form action="{{ route('checkout') }}" method="POST" class="mt-6">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-xl" style="font-family: 'Press Start 2P', cursive;">
                                Proceder al Pago
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="text-center bg-gray-800 p-12 rounded-lg">
                    <p class="text-2xl text-gray-400">Tu carrito está vacío.</p>
                    <a href="{{ route('storeGames') }}" class="inline-block mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg" style="font-family: 'Press Start 2P', cursive;">
                        Ir a la tienda
                    </a>
                </div>
            @endif
        </main>
    </div>
    <x-footer />
</body>
</html>