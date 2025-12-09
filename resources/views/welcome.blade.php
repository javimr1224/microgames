<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Microgames</title>
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

        <div class="relative w-full h-[400px] sm:h-[500px] lg:h-[600px] overflow-hidden">
            <img class="w-full h-full object-cover"
                src="{{ asset('videos/may-sitting-near-waterfall-pokemon-emerald-pixel-wallpaperwaifu-com-ezgif.com-video-to-gif-converter.gif') }}"
                alt="Microgames retro background">

            <div class="absolute inset-0 flex flex-col items-center justify-center px-4">
                <div class="flex flex-col items-center gap-4 sm:gap-6 animate-float">
                    <h4 class="text-stroke text-xl sm:text-2xl lg:text-3xl"
                        style="font-family: 'Press Start 2P', cursive;">
                        Play now!
                    </h4>
                    <img class="w-64 sm:w-80 lg:w-96" src="{{ asset('images/retro-games.png') }}" alt="Retro Games">
                </div>
                <a href="{{ route('game-menu') }}" class="relative animatable-button mt-4 w-48 sm:w-60 lg:w-72">
                    <img src="{{ asset('images/button.svg') }}" alt="Catalogo button" class="w-full">
                    <span
                        class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-xs sm:text-base lg:text-xl"
                        style="font-family: 'Press Start 2P', cursive; text-shadow: 2px 2px 4px #000000;">
                        Catálogo
                    </span>
                </a>
            </div>
            <div class="wave-svg-container">
                <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
                    <defs>
                        <path id="gentle-wave"
                            d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
                    </defs>
                    <g class="parallax">
                        <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(2,6,23,0.7)" />
                        <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(2,6,23,0.5)" />
                        <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(2,6,23,0.3)" />
                        <use xlink:href="#gentle-wave" x="48" y="7" fill="rgb(2,6,23)" />
                    </g>
                </svg>
            </div>
        </div>

        <section class="bg-[#020617] text-white py-8 sm:py-12 lg:py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto text-center">
                <div class="flex flex-col sm:flex-row text-center justify-center items-center gap-2 sm:gap-4">
                    <h2 class="text-xs sm:text-lg md:text-2xl lg:text-3xl"
                        style="font-family: 'Press Start 2P', cursive; color: #ffff;">
                        Insert Coin para empezar
                    </h2>
                    <img class="w-12 sm:w-16 md:w-20" src="{{ asset('videos/coin_big 1.gif') }}" alt="Coin">
                </div>
                <p class="max-w-3xl mx-auto mt-4 text-sm sm:text-base lg:text-xl text-gray-300 px-4">
                    Explora la historia y la diversión de los arcades y consolas clásicas.
                    La nostalgia del pixel te espera en cada pantalla.
                </p>
            </div>

            <div class="flex flex-wrap justify-center mt-6 sm:mt-8 gap-3 sm:gap-6 lg:gap-8 px-4"
                style="font-size: 8px; font-family: 'Press Start 2P', cursive;">
                <div class="rounded-2xl border p-2 transition-all duration-300 hover:scale-105">
                    <button id="filter-popular-welcome"
                        class="px-2 sm:px-3 text-[8px] sm:text-[10px]">Populares</button>
                </div>
                <div class="rounded-2xl border p-2 transition-all duration-300 hover:scale-105">
                    <button class="px-2 sm:px-3 text-[8px] sm:text-[10px]">Gratuitos</button>
                </div>
                <div class="rounded-2xl border p-2 transition-all duration-300 hover:scale-105">
                    <button id="filter-newest-welcome" class="px-2 sm:px-3 text-[8px] sm:text-[10px]">Novedades</button>
                </div>
                <div class="rounded-2xl border p-2 transition-all duration-300 hover:scale-105">
                    <button class="px-2 sm:px-3 text-[8px] sm:text-[10px]">Arcade</button>
                </div>
            </div>

            <div class="flex justify-center sm:justify-end mt-6 sm:mt-8 px-4 sm:px-6">
                <a href="{{ route('game-menu') }}" style="color: #E9C46A; font-family: 'Press Start 2P', cursive;"
                    class="underline text-center text-xs sm:text-sm">
                    <span class="flex items-center justify-center gap-2">
                        Explora
                        <svg width="12" height="8" viewBox="0 0 17 10" fill="none" xmlns="http://www.w3.org/2000/svg"
                            class="sm:w-[17px] sm:h-[10px]" style="transform: rotate(-90deg);">
                            <path
                                d="M8.78785 7.24942L8.5 7.24942L1.52399 -6.76478e-07L-6.64563e-08 1.52034L8.5 10L17 1.52034L15.476 -6.66157e-08L8.78785 7.24942Z"
                                fill="#E9C46A" />
                        </svg>
                    </span>
                    <span class="hidden sm:inline">nuestros juegos</span>
                </a>
            </div>

            <div id="welcome-games-container"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mt-8 max-w-7xl mx-auto px-4">
            </div>

            <section class="flex justify-center items-center py-8 sm:py-12 lg:py-16">
                <a href="{{ route('game-menu') }}"
                    class="relative animatable-button w-64 sm:w-96 lg:w-[500px] h-[80px] px-4">
                    <img src="{{ asset('images/button.png') }}" alt="Catalogo button" class="w-[120rem] h-[4.2rem]">
                    <span
                        class="absolute left-1/2 top-1/3 -translate-x-1/2 -translate-y-1/2 sm:text-sm lg:text-base whitespace-nowrap"
                        style="font-family: 'Press Start 2P', cursive; text-shadow: 2px 2px 4px #000000;">
                        Explora los juegos
                    </span>
                </a>
            </section>

        </section>

        <section class="w-full bg-[#020617] py-8 sm:py-12 lg:py-16 px-4 flex justify-center overflow-hidden">
            <div class="w-full max-w-6xl flex flex-col items-center">
                <div class="mb-8 sm:mb-12 w-full flex flex-col items-center lg:items-end px-4">
                    <h1 style="font-family: 'Press Start 2P';"
                        class="text-white text-sm sm:text-xl lg:text-2xl xl:text-3xl text-left lg:text-left leading-relaxed mb-4">
                        Una pequeña muestra<br class="hidden sm:block"> de nuestros juegos
                    </h1>
                    <p style="font-family: 'Helvetica Neue';"
                        class="text-white/70 text-xs sm:text-sm lg:text-base text-left lg:text-left max-w-lg">
                        Descubre algunos de los minijuegos que hemos creado,
                        todos inspirados en el estilo retro para que disfrutes
                        de una experiencia divertida y nostálgica.
                    </p>
                </div>

                <div class="relative w-full min-h-[1100px] sm:min-h-[1450px] lg:min-h-[1450px]">

                    <img src="{{ asset('videos/dancingcowboydone21.gif') }}"
                        class="w-8 sm:w-12 lg:w-[90px] absolute top-0 left-[5%] sm:left-[15%] lg:left-[29%]"
                        style="image-rendering: pixelated;">

                    <div
                        class="game-card-1 absolute top-[50px] sm:top-[72px] left-[2%] sm:left-[5%] lg:left-[0.1%] w-[90%] sm:w-[400px] lg:w-[440px]">
                        <div class="rounded-lg p-2 sm:p-3 shadow-2xl h-[200px] sm:h-[250px] lg:h-[300px]">
                            <img src="{{ asset('images/video-snake.png') }}" class="rounded-xl w-full h-full"
                                style="image-rendering: pixelated;">
                        </div>
                    </div>
                    <div
                        class="absolute top-[280px] sm:top-[350px] lg:top-[280px] right-[2%] sm:right-[5%] lg:left-[450px] w-[90%] sm:w-[400px] lg:w-[580px]">
                        <img src="{{ asset('videos/pjpa72551.gif') }}"
                            class="w-8 sm:w-12 lg:w-[90px] absolute -top-8 sm:-top-10 lg:-top-12 left-[84%]"
                            style="transform: scaleX(-1) rotate(-5deg); image-rendering: pixelated;">

                        <div class="rounded-lg mt-6 sm:mt-8 p-2 sm:p-3 shadow-2xl">
                            <img src="{{ asset('images/video-tetris.png') }}" class="game-card-2 rounded-xl w-full"
                                style="image-rendering: pixelated;">
                        </div>
                    </div>

                    <img src="{{ asset('videos/walk_011.gif') }}"
                        class="w-8 sm:w-12 lg:w-[90px] absolute top-[520px] sm:top-[620px] lg:top-[597px] left-[5%]"
                        style="image-rendering: pixelated;">

                    <div
                        class="game-card-3 absolute top-[580px] sm:top-[700px] lg:top-[733px] left-[2%] sm:left-[5%] lg:left-[0.1%] w-[90%] sm:w-[480px] lg:w-[590px]">
                        <div class="rounded-lg p-2 sm:p-3 shadow-2xl h-[200px] sm:h-[250px] lg:h-[300px]">
                            <img src="{{ asset('images/video-brekout.png') }}" class="rounded-xl w-full h-full"
                                style="image-rendering: pixelated;">
                        </div>
                    </div>

                    <img src="{{ asset('videos/hideyoshianimpreviewexport 1.gif') }}"
                        class="w-8 sm:w-12 lg:w-[90px] absolute top-[820px] sm:top-[900px] lg:top-[945px] right-[5%] lg:right-[21%]"
                        style="image-rendering: pixelated;">

                    <div
                        class="game-card-4 absolute top-[880px] sm:top-[1000px] lg:top-[1000px] right-[2%] sm:right-[5%] lg:left-[400px] w-[90%] sm:w-[400px] lg:w-[580px]">
                        <div class="rounded-lg mt-6 sm:mt-8 p-2 sm:p-3 shadow-2xl">
                            <img src="{{ asset('images/video-pong.png') }}" class="rounded-xl w-full"
                                style="image-rendering: pixelated;">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="relative bg-white">

            <div class="w-full overflow-hidden leading-[0]">
                <svg style="background-color: rgba(2,6,23,23);" class="relative block w-full h-[100px]"
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28"
                    preserveAspectRatio="none" shape-rendering="auto">

                    <defs>
                        <path id="gentle-wave"
                            d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
                    </defs>

                    <g class="parallax">
                        <use href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7)" />
                        <use href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
                        <use href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
                        <use href="#gentle-wave" x="48" y="9" fill="#ffffff" />
                    </g>
                </svg>
            </div>

            <section class="bg-white min-h-[200px] relative z-10" id="desarrollador-section">
                <div class="flex flex-col max-w-7xl mx-auto mt-12 py-12 px-4 text-center items-center justify-center">
                    <h2 style="font-family: 'Press Start 2P';" class="text-4xl text-gray-900">Desarrollador</h2>
                    <h4 style="font-family: 'Helvetica Neue';" class="text-[#020617] mt-7">Nuestro equipo de
                        desarrolladores solo se compone del creador pero
                        con <br> el potencial de está aplicación, pronto tendremos un equipo exitoso y con <br>
                        experiencia en el mercado</h4>
                    <img class="py-6 w-[220px]" src="{{ asset('images/javi.png') }}" alt="">
                    <h3 style="font-family: 'Press Start 2P';" class="text-2xl text-gray-900">Javier <br> Martínez
                    </h3>
                    <h4 style="font-family: 'Helvetica Neue';" class="mb-12 text-[#020617] py-0">Fundador de
                        Microgames</h4>
                </div>
                <div class="flex flex-col max-w-7xl mx-auto px-4 text-center items-center justify-center">
                    <div class="flex flex-col max-w-7xl mx-auto py-10 px-4 text-center items-center justify-center">
                        <h2 style="font-family: 'Press Start 2P';" class="text-4xl text-gray-900">Experiencia de <br>
                            nuestros usuarios</h2>
                        <h2 style="font-family: 'Helvetica Neue';" class="text-[#020617] mt-10 text-left text-2xl">
                            “Cada
                            uno de los juegos de la
                            página web <br> Microgames ha conseguido que disfrute de <br> una experiencia divertida y, al mismo tiempo, <br> desestresante con sus minijuegos arcade”</h2>
                    </div>
                </div>
                <div class="flex justify-center items-center">
                    <div class="flex items-center mr-[90px] mb-12">
                        <img class="w-[64px] h-[64px]" src="{{ asset('images/albi.png') }}" alt="imagen fundador">
                        <div class="flex flex-col mx-[22px]">
                            <h3 style="font-family: 'Press Start 2P';" class="text-2xl text-gray-900">Alba Espejo</h3>
                            <h4 style="font-family: 'Helvetica Neue';" class="text-[#020617]">CEO de BioVolt Systems
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center py-16 items-center min-h-[300px] sm:min-h-[300px] lg:min-h-[300px]">
                    <div style="box-shadow: 2px 2px 4px #3b3b3b;"
                        class="bg-[#e8e8e8] p-16 rounded-2xl flex flex-row items-center gap-8 mb-12 w-[1000px]">
                        <div class="max-w-lg">
                            <h2 style="font-family: 'Helvetica Neue'; white-space: nowrap;"
                                class="text-3xl text-[#020617] font-medium">
                                Consulta nuestros planes de precios
                            </h2>
                            <p style="font-family: 'Helvetica Neue';" class="text-[#020617] py-3">
                                ¡Elige tu nivel de jugador y desbloquea el poder del píxel! <br>
                                Cada jugador merece su propia aventura por eso mismo <br> tenemos planes que incluyen
                                acceso inmediato a juegos retro, <br> soporte premium y actualizaciones constantes de
                                contenido.
                            </p>
                        </div>

                        <div class="relative animatable-button px-6">
                            <a href="{{ route('storeGames') }}" class="relative mt-4 w-1900 sm:w-1900 lg:w-1900">
                                <img src="{{ asset('images/Vectorized SVG.svg') }}" alt="Precios button"
                                    class="w-[1500px] h-[80px]">
                                <span
                                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 text-xs sm:text-base lg:text-base"
                                    style="font-family: 'Press Start 2P', cursive; text-shadow: 2px 2px 4px #000000; white-space: nowrap;">
                                    Ver planes
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="bg-white min-h-[200px]"></div>
    </div>

    <x-footer />
</body>

</html>