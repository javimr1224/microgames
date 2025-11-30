<header class="sticky top-0 z-50"
    style="width: 100%; background: #020617; box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.75); font-family: 'Press Start 2P', cursive;">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-6">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('images/image.png') }}" alt="Microgames Logo" class="mr-3 h-8 w-8">
            <span class="text-lg text-white">Microgames</span>
        </a>
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
            <a href="{{ route('help') }}" class="text-gray-300 hover:text-white">Ayuda</a>
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
                    <a href="{{ route('storeGames') }}"
                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">Comprar
                        juegos</a>
                </div>
            </div>
        </nav>
        <!-- Search bar (hidden by default) -->
        <div id="header-search-bar" class="hidden absolute top-full left-1/2 -translate-x-1/2 mt-2 w-full max-w-md">
            <div class="relative">
                <input id="header-search-input" type="text" placeholder="Buscar juegos..." class="w-full p-3 pl-10 text-white bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:border-blue-500" />
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <div id="header-search-results" class="mt-2 bg-gray-800 rounded-lg shadow-lg"></div>
        </div>

        <div class="flex items-center">
            <button id="header-search-icon" class="cursor-pointer">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-label="Search">
                    <title>Search</title>
                    <path
                        d="M17.2116 14.8272V0H0V16.8627H14.6611L21.7184 24L24 21.6925L17.2116 14.8272ZM13.9844 13.599H3.22718V3.26375H13.9844V13.599ZM6.99222 12.511H4.30291V9.79126H6.99222V12.511Z"
                        fill="white" />
                </svg>
            </button>
            <button id="dark-mode-toggle" class="ml-6 cursor-pointer">
                <svg id="moon-icon" width="23" height="24" viewBox="0 0 23 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                    aria-label="Modo oscuro">
                    <title>Modo oscuro</title>
                    <path
                        d="M10.9316 23.8333C17.3398 23.8333 22.68 18.474 22.68 11.9167C22.68 5.35935 17.3398 0 10.806 0C9.61229 0 8.41861 0.189154 7.22492 0.567461C6.65949 0.756615 6.28254 1.32408 6.28254 1.95459C6.34537 2.5851 6.78514 3.08951 7.4134 3.15256C10.8688 3.65697 13.4446 6.62037 13.4446 10.0882C13.4446 13.9343 10.3034 17.0869 6.47102 17.0869C4.90038 17.0869 3.39257 16.5825 2.13606 15.5736C1.63345 15.1953 0.942373 15.1953 0.502596 15.5736C-9.53674e-06 15.9519 -0.12566 16.6455 0.125643 17.213C2.26171 21.2482 6.34537 23.8333 10.9316 23.8333ZM12.942 2.90035C17.0257 3.84612 20.0413 7.56614 20.0413 11.9167C20.0413 17.0238 15.8948 21.1852 10.806 21.1852C8.98404 21.1852 7.22492 20.6808 5.77994 19.672C6.03124 19.672 6.28254 19.672 6.53384 19.672C11.8112 19.672 16.1461 15.3214 16.1461 10.0251C16.2718 7.25088 15.0153 4.66579 12.942 2.90035Z"
                        fill="white" />
                </svg>
                <svg id="sun-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            </button>
            @if (Route::has('login'))
                @auth
                    <div class="relative dropdown-container ml-6">
                        <button class="text-sm font-semibold text-gray-300 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </button>
                        <div class="absolute mt-2 py-2 w-48 bg-gray-800 rounded-md shadow-xl z-20 dropdown-content right-0">
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user mr-2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                Mi perfil
                            </a>
                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-server mr-2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                                Juegos
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white w-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out mr-2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                    Logout
                                a </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="ml-12 relative animatable-button">
                        <img src="{{ asset('images/button.png') }}" alt="Login button" style="width: 100px;">
                        <span class="absolute left-1/2 -translate-x-1/2 -translate-y-1/2"
                            style="color: white; font-family: 'Press Start 2P', cursive; text-shadow: 2px 2px 4px #000000; font-size: 13px; top: 45%;">Login</span>
                    </a>
                @endauth
            @endif
        </div>
    </div>
</header>
