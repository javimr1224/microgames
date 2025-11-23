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

        <div class="relative w-full max-h-[600px] overflow-hidden">
            <img class="w-full h-full object-cover"
                src="{{ asset('videos/may-sitting-near-waterfall-pokemon-emerald-pixel-wallpaperwaifu-com-ezgif.com-video-to-gif-converter.gif') }}"
                alt="Microgames retro background">

            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <div class="flex flex-col items-center gap-6 animate-float">
                    <h4 class="text-stroke" style="font-family: 'Press Start 2P', cursive; font-size: 30px;">Play now!
                    </h4>
                    <img class="imagen-principal w-96" src="{{ asset('images/retro-games.png') }}" alt="Retro Games">
                </div>
                <a href="{{ route('game-menu') }}" class="relative animatable-button mt-3">
                    <img src="{{ asset('images/button.svg') }}" alt="Catalogo button" style="width: 300px;">
                    <span class="absolute left-1/2 -translate-x-1/2 -translate-y-1/2"
                        style="font-family: 'Press Start 2P', cursive; text-shadow: 2px 2px 4px #000000; font-size: 20px; top: 45%;">Catálogo</span>
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

        <section class="bg-[#020617] text-white py-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto text-center">
                <h2 class="text-l font-extrabold tracking-tight sm:text-sm md:text-3xl"
                    style="font-family: 'Press Start 2P', cursive; color: #ffff;">
                    Insert Coin para empezar
                </h2>
                <p class="mt-6 max-w-3xl mx-auto text-xl text-gray-300">
                    Explora la historia y la diversión de los arcades y consolas clásicas. La nostalgia del pixel te
                    espera en cada pantalla. </p>
            </div>
            <div class="flex justify-end mt-8 ">
                <a href="#" style="color: #E9C46A; font-family: 'Press Start 2P', cursive;"
                    class="mx-6 underline text-center">
                    <span class="flex items-center justify-center ml-4 pl-4">
                        Explora
                        <svg width="17" height="10" viewBox="0 0 17 10" fill="none"
                            xmlns="http://www.w3.org/2000/svg" style="transform: rotate(-90deg); margin-left: 8px;">
                            <path
                                d="M8.78785 7.24942L8.5 7.24942L1.52399 -6.76478e-07L-6.64563e-08 1.52034L8.5 10L17 1.52034L15.476 -6.66157e-08L8.78785 7.24942Z"
                                fill="#E9C46A" />
                        </svg>
                    </span>
                    nuestros juegos
                </a>
            </div>

            <div class="flex justify-center mt-8 gap-8">
                <div class="bg-neutral-primary-soft block max-w-sm border border-default rounded-base shadow-xs">
                    <a href="#">
                        <img class="rounded-md border" src="{{ asset('images/snake.png') }}" alt="" />
                    </a>
                    <div class="p-6 text-start">
                        <a href="#">
                            <h5 style="font-family: 'Press Start 2P';" class="mt-1 mb-6 text-l">SNAKE</h5>
                            <p style="font-family: 'Helvetica Neue';" class="mt-2">Texto</p>
                        </a>
                    </div>
                </div>
                <div class="bg-neutral-primary-soft block max-w-sm border border-default rounded-base shadow-xs">
                    <a href="#">
                        <img class="rounded-md border" src="{{ asset('images/breakout.png') }}" alt="" />
                    </a>
                    <div class="p-6 text-start">
                        <a href="#">
                            <h5 style="font-family: 'Press Start 2P';" class="mt-1 mb-6 text-l">BREAKOUT</h5>
                            <p style="font-family: 'Helvetica Neue';" class="mt-2">Texto</p>

                        </a>
                    </div>
                </div>
                <div class="bg-neutral-primary-soft block max-w-sm border border-default rounded-base shadow-xs">
                    <a href="#">
                        <img class="rounded-md border" src="{{ asset('images/tetris.png') }}" alt="" />
                    </a>
                    <div class="p-4 text-start">
                        <a href="#">
                            <h5 style="font-family: 'Press Start 2P';" class="mt-1 mb-6 text-l">TETRIS</h5>
                            <p style="font-family: 'Helvetica Neue';" class="mt-2">Texto</p>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <x-footer />
    </div>
</body>

</html>
