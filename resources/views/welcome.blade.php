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

    @vite('resources/css/app.css')

    <style>
        .wave-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 150px;
            overflow: hidden;
            pointer-events: none;
        }

        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200%;
            height: 100%;
            background: rgba(020617);
            clip-path: polygon(
                0% 60%,
                5% 55%,
                10% 50%,
                15% 48%,
                20% 50%,
                25% 55%,
                30% 60%,
                35% 63%,
                40% 60%,
                45% 55%,
                50% 50%,
                55% 48%,
                60% 50%,
                65% 55%,
                70% 60%,
                75% 63%,
                80% 60%,
                85% 55%,
                90% 50%,
                95% 48%,
                100% 50%,
                100% 100%,
                0% 100%
            );
            animation: wave-flow 12s linear infinite;
        }

        @keyframes wave-flow {
            0% {
                transform: translateX(0);
            }
            
            100% {
                transform: translateX(-50%);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .text-stroke {
            text-shadow:
                -2px -2px 0 #000,
                2px -2px 0 #000,
                -2px 2px 0 #000,
                2px 2px 0 #000,
                0 0 10px rgba(233, 196, 106, 0.5);
        }
    </style>

</head>

<body class="antialiased bg-gray-900 text-white font-sans flex flex-col min-h-screen">
    <div class="flex-grow">
        <header class="sticky top-0 z-50"
            style="width: 100%; background: #020617; box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.75); font-family: 'Press Start 2P', cursive;">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-6">
                <div class="flex items-center">
                    <img src="{{ asset('images/image.png') }}" alt="Microgames Logo" class="mr-3 h-8 w-8">
                    <span class="text-lg">Microgames</span>
                </div>
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="#" class="text-[12] text-gray-300 hover:text-white flex items-center">
                        <span>Categorias</span>
                        <svg width="17" height="10" viewBox="0 0 17 10" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="ml-2">
                            <path
                                d="M8.78785 7.24942L8.5 7.24942L1.52399 -6.76478e-07L-6.64563e-08 1.52034L8.5 10L17 1.52034L15.476 -6.66157e-08L8.78785 7.24942Z"
                                fill="#E9C46A" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white flex items-center">
                        <span>Juegos</span>
                        <svg width="17" height="10" viewBox="0 0 17 10" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="ml-2">
                            <path
                                d="M8.78785 7.24942L8.5 7.24942L1.52399 -6.76478e-07L-6.64563e-08 1.52034L8.5 10L17 1.52034L15.476 -6.66157e-08L8.78785 7.24942Z"
                                fill="#E9C46A" />
                        </svg>
                    </a>

                    <a href="#" class="text-gray-300 hover:text-white">Ayuda</a>
                    <a href="#" class="text-gray-300 hover:text-white flex items-center">
                        <span>Tienda</span>
                        <svg width="17" height="10" viewBox="0 0 17 10" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="ml-2">
                            <path
                                d="M8.78785 7.24942L8.5 7.24942L1.52399 -6.76478e-07L-6.64563e-08 1.52034L8.5 10L17 1.52034L15.476 -6.66157e-08L8.78785 7.24942Z"
                                fill="#E9C46A" />
                        </svg>
                    </a>
                </nav>
                <div class="flex items-center">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-semibold text-gray-300 hover:text-white">Dashboard</a>
                        @else
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" aria-label="Log in">
                                <title>Search</title>
                                <path
                                    d="M17.2116 14.8272V0H0V16.8627H14.6611L21.7184 24L24 21.6925L17.2116 14.8272ZM13.9844 13.599H3.22718V3.26375H13.9844V13.599ZM6.99222 12.511H4.30291V9.79126H6.99222V12.511Z"
                                    fill="white" />
                            </svg>
                            </a>
                            <svg width="23" height="24" viewBox="0 0 23 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" aria-label="Register" class="ml-8">
                                <title>Modo oscuro</title>
                                <path
                                    d="M10.9316 23.8333C17.3398 23.8333 22.68 18.474 22.68 11.9167C22.68 5.35935 17.3398 0 10.806 0C9.61229 0 8.41861 0.189154 7.22492 0.567461C6.65949 0.756615 6.28254 1.32408 6.28254 1.95459C6.34537 2.5851 6.78514 3.08951 7.4134 3.15256C10.8688 3.65697 13.4446 6.62037 13.4446 10.0882C13.4446 13.9343 10.3034 17.0869 6.47102 17.0869C4.90038 17.0869 3.39257 16.5825 2.13606 15.5736C1.63345 15.1953 0.942373 15.1953 0.502596 15.5736C-9.53674e-06 15.9519 -0.12566 16.6455 0.125643 17.213C2.26171 21.2482 6.34537 23.8333 10.9316 23.8333ZM12.942 2.90035C17.0257 3.84612 20.0413 7.56614 20.0413 11.9167C20.0413 17.0238 15.8948 21.1852 10.806 21.1852C8.98404 21.1852 7.22492 20.6808 5.77994 19.672C6.03124 19.672 6.28254 19.672 6.53384 19.672C11.8112 19.672 16.1461 15.3214 16.1461 10.0251C16.2718 7.25088 15.0153 4.66579 12.942 2.90035Z"
                                    fill="white" />
                            </svg>
                            <a href="{{ route('login') }}" class="ml-12 relative">
                                <img src="{{ asset('images/button.png') }}" alt="Login button" style="width: 100px;">
                                <span class="absolute left-1/2 -translate-x-1/2 -translate-y-1/2"
                                    style="font-family: 'Press Start 2P', cursive; text-shadow: 2px 2px 4px #000000; font-size: 13px; top: 45%;">Login</span>
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </header>

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
                <a href="#" class="relative">
                    <img src="{{ asset('images/button.png') }}" alt="Catalogo button"
                        style="width: 230px; left-width: 230px;">
                    <span class="absolute left-1/2 -translate-x-1/2 -translate-y-1/2"
                        style="font-family: 'Press Start 2P', cursive; text-shadow: 2px 2px 4px #000000; font-size: 20px; top: 45%;">Catálogo</span>
                </a>
            </div>

            <div class="wave-container">
                <div class="wave"></div>
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
        </section>

        <footer class="w-full bg-[#020617] text-white py-12 relative overflow-hidden"
            style="font-family: 'Helvetica Neue', sans-serif;">
            <div class="absolute top-0 left-0 right-0 h-px bg-white opacity-20"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 mb-12">

                    <div class="md:col-span-5 space-y-6">
                        <div class="flex items-end gap-2">
                            <img src="{{ asset('images/image.png') }}" alt="Microgames Logo" class="h-8 w-8">
                            <div class="bg-clip-text text-transparent"
                                style="font-family: 'Press Start 2P', cursive; font-size: 21px; letter-spacing: -0.21px; background-image: linear-gradient(rgb(255, 255, 255) 0%, rgba(255, 255, 255, 0) 100%), linear-gradient(90deg, rgb(255, 244, 212) 0%, rgb(255, 244, 212) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                Microgames
                            </div>
                        </div>
                        <p class="text-xxl leading-[1.3] opacity-60" style="letter-spacing: -0.48px;">
                            Pequeños juegos, grandes desafíos.<br> Entra en el universo retro de MicroGames y revive la
                            magia del píxel.<br> Juega, compite y desbloquea logros en cada partida.
                        </p>
                    </div>

                    <div class="md:col-span-2 space-y-4" style="font-size: 18px; letter-spacing: -0.48px;">
                        <div class="opacity-50 leading-[1.3]">Páginas</div>
                        <div class="space-y-3">
                            <p class="leading-[1.3]"><a href="#"
                                    class="hover:text-[#e9c46a] transition-colors">Juegos</a></p>
                            <p class="leading-[1.3]"><a href="#"
                                    class="hover:text-[#e9c46a] transition-colors">Ayuda</a></p>
                            <p class="leading-[1.3]"><a href="#"
                                    class="hover:text-[#e9c46a] transition-colors">Catálogo</a></p>
                            <p class="leading-[1.3]"><a href="#"
                                    class="hover:text-[#e9c46a] transition-colors">Tienda</a></p>
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-4" style="font-size: 18px; letter-spacing: -0.48px;">
                        <div class="opacity-50 leading-[1.3]">Secciones extra</div>
                        <div class="space-y-4">
                            <p class="leading-[1.3]"><a href="#"
                                    class="hover:text-[#e9c46a] transition-colors">Dark theme</a></p>
                            <p class="leading-[1.3] text-[#e9c46a]"><a href="#">Light theme</a></p>
                        </div>
                    </div>

                    <div class="md:col-span-3 space-y-6" style="font-size: 18px; letter-spacing: -0.48px;">
                        <div class="opacity-50 leading-[1.3]">Contacto</div>
                        <div class="space-y-6">
                            <p class="leading-[1.3]"><a href="#"
                                    class="hover:text-[#e9c46a] transition-colors">Soporte técnico</a></p>
                            <p class="leading-[1.3]"><a href="#"
                                    class="hover:text-[#e9c46a] transition-colors">FAQ / Guía de inicio</a></p>
                        </div>
                    </div>

                </div>

                <div class="flex flex-col md:flex-row justify-between items-center gap-6 pt-8">
                    <p class="opacity-50 text-xxl whitespace-nowrap"
                        style="letter-spacing: -0.48px; line-height: 1.3;">© 2025-2026 All Rights Reserved.</p>
                    <div class="flex items-center gap-4 md:gap-[25px]">
                        <a href="#" class="w-10 h-10 hover:opacity-80 transition-opacity">
                            <img alt="Spotify" class="w-full h-full object-cover"
                                src="{{ asset('images/spoti.png') }}" />
                        </a>
                        <a href="#" class="w-10 h-10 hover:opacity-80 transition-opacity">
                            <img alt="YouTube" class="w-full h-full object-cover"
                                src="{{ asset('images/yt.png') }}" />
                        </a>
                        <a href="#" class="w-10 h-10 hover:opacity-80 transition-opacity">
                            <img alt="TikTok" class="w-full h-full object-cover"
                                src="{{ asset('images/tiktok.png') }}" />
                        </a>
                        <a href="#" class="w-10 h-10 hover:opacity-80 transition-opacity">
                            <img alt="Discord" class="w-full h-full object-cover"
                                src="{{ asset('images/discord.png') }}" />
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>