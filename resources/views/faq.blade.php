<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Microgames - FAQ</title>
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

        <div class="relative w-full h-[200px] sm:h-[300px] lg:h-[400px] overflow-hidden flex items-center justify-center">
            <h1 class="text-4xl" style="font-family: 'Press Start 2P', cursive;">FAQ / Guía de inicio</h1>
        </div>
        
        <section class="bg-[#020617] text-white py-8 sm:py-12 lg:py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto text-left">
                <h2 class="text-2xl" style="font-family: 'Press Start 2P', cursive;">Preguntas Frecuentes</h2>
                <br>
                <h3 class="text-xl" style="font-family: 'Press Start 2P', cursive;">¿Cómo puedo jugar?</h3>
                <p class="mt-2">Simplemente navega a la sección de juegos y haz clic en el que quieras jugar.</p>
                <br>
                <h3 class="text-xl" style="font-family: 'Press Start 2P', cursive;">¿Necesito una cuenta?</h3>
                <p class="mt-2">No necesitas una cuenta para jugar a los juegos gratuitos. Sin embargo, para guardar tu progreso y puntuaciones, necesitarás registrarte.</p>
                <br>
                <h3 class="text-xl" style="font-family: 'Press Start 2P', cursive;">¿Cómo guardo mi puntuación?</h3>
                <p class="mt-2">Inicia sesión en tu cuenta y tu puntuación se guardará automáticamente al final de cada partida.</p>
            </div>
        </section>

    </div>

    <x-footer />
</body>

</html>
