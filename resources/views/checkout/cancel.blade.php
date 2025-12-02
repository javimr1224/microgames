<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago Cancelado - Microgames</title>
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

        <main class="max-w-7xl mx-auto mt-12 py-12 px-4 text-center">
            <h1 class="text-4xl font-bold mb-4" style="font-family: 'Press Start 2P', cursive; color: #dc3545;">¡Pago Cancelado!</h1>

            @if(session('error'))
                <div class="bg-red-500 text-white p-4 rounded-lg mb-6 max-w-2xl mx-auto">
                    <p class="font-bold">Detalle del error:</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <p class="text-lg text-gray-300 mb-8">Tu pago ha sido cancelado o ha ocurrido un error, puedes intentarlo de nuevo o explorar otros juegos de mientras</p>
            <a href="{{ url('/') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-xl" style="font-family: 'Press Start 2P', cursive;">
                Volver al inicio
            </a>
        </main>
    </div>

    <x-footer />
</body>

</html>
