<header class="sticky top-0 z-50"
    style="width: 100%; background: #020617; box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.75); font-family: 'Press Start 2P', cursive;">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-6">
        <div class="flex items-center">
            <img src="{{ asset('images/image.png') }}" alt="Microgames Logo" class="mr-3 h-8 w-8">
            <span class="text-lg">Microgames</span>
        </div>
        <nav class="hidden md:flex items-center space-x-8">
            <div class="relative dropdown-container">
                <button class="text-[12] text-gray-300 hover:text-white flex items-center">
                    <span>Categorias</span>
                    <svg width="17" height="10" viewBox="0 0 17 10" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="ml-2">
                        <path
                            d="M8.78785 7.24942L8.5 7.24942L1.52399 -6.76478e-07L-6.64563e-08 1.52034L8.5 10L17 1.52034L15.476 -6.66157e-08L8.78785 7.24942Z"
                            fill="#E9C46A" />
                    </svg>
                </button>
                <div class="absolute mt-2 py-2 w-48 bg-gray-800 rounded-md shadow-xl z-20 dropdown-content">
                    <a href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Arcade</a>
                    <a href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Aventura</a>
                    <a href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Estrategia</a>
                </div>
            </div>
            <div class="relative dropdown-container">
                <button class="text-gray-300 hover:text-white flex items-center">
                    <span>Juegos</span>
                    <svg width="17" height="10" viewBox="0 0 17 10" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="ml-2">
                        <path
                            d="M8.78785 7.24942L8.5 7.24942L1.52399 -6.76478e-07L-6.64563e-08 1.52034L8.5 10L17 1.52034L15.476 -6.66157e-08L8.78785 7.24942Z"
                            fill="#E9C46A" />
                    </svg>
                </button>
                <div class="absolute mt-2 py-2 w-48 bg-gray-800 rounded-md shadow-xl z-20 dropdown-content">
                    <a href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Más
                        Jugados</a>
                    <a href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Nuevos</a>
                    <a href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Recomendados</a>
                </div>
            </div>
            <a href="#" class="text-gray-300 hover:text-white">Ayuda</a>
            <div class="relative dropdown-container">
                <button class="text-gray-300 hover:text-white flex items-center">
                    <span>Tienda</span>
                    <svg width="17" height="10" viewBox="0 0 17 10" fill="none"
                        xmlns="http://www.w3.org/2000/svg" class="ml-2">
                        <path
                            d="M8.78785 7.24942L8.5 7.24942L1.52399 -6.76478e-07L-6.64563e-08 1.52034L8.5 10L17 1.52034L15.476 -6.66157e-08L8.78785 7.24942Z"
                            fill="#E9C46A" />
                    </svg>
                </button>
                <div class="absolute mt-2 py-2 w-48 bg-gray-800 rounded-md shadow-xl z-20 dropdown-content">
                    <a href="#"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Comprar
                        juegos</a>
                </div>
            </div>
        </nav>
        <div class="flex items-center">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="text-sm font-semibold text-gray-300 hover:text-white">Dashboard</a>
                @else
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                        aria-label="Log in">
                        <title>Search</title>
                        <path
                            d="M17.2116 14.8272V0H0V16.8627H14.6611L21.7184 24L24 21.6925L17.2116 14.8272ZM13.9844 13.599H3.22718V3.26375H13.9844V13.599ZM6.99222 12.511H4.30291V9.79126H6.99222V12.511Z"
                            fill="white" />
                    </svg>
                    </a>
                    <svg width="23" height="24" viewBox="0 0 23 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                        aria-label="Register" class="ml-8">
                        <title>Modo oscuro</title>
                        <path
                            d="M10.9316 23.8333C17.3398 23.8333 22.68 18.474 22.68 11.9167C22.68 5.35935 17.3398 0 10.806 0C9.61229 0 8.41861 0.189154 7.22492 0.567461C6.65949 0.756615 6.28254 1.32408 6.28254 1.95459C6.34537 2.5851 6.78514 3.08951 7.4134 3.15256C10.8688 3.65697 13.4446 6.62037 13.4446 10.0882C13.4446 13.9343 10.3034 17.0869 6.47102 17.0869C4.90038 17.0869 3.39257 16.5825 2.13606 15.5736C1.63345 15.1953 0.942373 15.1953 0.502596 15.5736C-9.53674e-06 15.9519 -0.12566 16.6455 0.125643 17.213C2.26171 21.2482 6.34537 23.8333 10.9316 23.8333ZM12.942 2.90035C17.0257 3.84612 20.0413 7.56614 20.0413 11.9167C20.0413 17.0238 15.8948 21.1852 10.806 21.1852C8.98404 21.1852 7.22492 20.6808 5.77994 19.672C6.03124 19.672 6.28254 19.672 6.53384 19.672C11.8112 19.672 16.1461 15.3214 16.1461 10.0251C16.2718 7.25088 15.0153 4.66579 12.942 2.90035Z"
                            fill="white" />
                    </svg>
                    <a href="{{ route('login') }}" class="ml-12 relative animatable-button">
                        <img src="{{ asset('images/button.png') }}" alt="Login button" style="width: 100px;">
                        <span class="absolute left-1/2 -translate-x-1/2 -translate-y-1/2"
                            style="font-family: 'Press Start 2P', cursive; text-shadow: 2px 2px 4px #000000; font-size: 13px; top: 45%;">Login</span>
                    </a>
                @endauth
            @endif
        </div>
    </div>
</header>
