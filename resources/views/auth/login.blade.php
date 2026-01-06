<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

    <x-header />

    <x-guest-layout>
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="flex justify-center">
                <h1 class="p-12 text-3xl text-[#264653] text-nowrap" style="font-family: 'Press Start 2P', cursive; text-shadow: 1px 1px 4px #00000061;">
                    Inicia sesión</h1>
            </div>
            <div>
                <x-input-label for="email" />
                <x-text-input id="email" class="block mt-1 w-full" placeholder="Correo electrónico" type="email"
                    name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4 relative">
                <x-input-label for="password" />

                <x-text-input id="password" class="block mt-1 w-full pr-10" placeholder="Contraseña" type="password"
                    name="password" required autocomplete="current-password" />

                <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                    <img id="eyeOpened" src="{{ asset('images/eye-opened.png') }}" class="w-6 h-auto text-gray-700" alt="Show Password">
                    <img id="eyeClosed" src="{{ asset('images/eye-closed.png') }}" class="w-6 h-auto text-gray-700 hidden" alt="Hide Password">
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-center mt-4">
                <button type="submit" class="relative animatable-button">
                    <img src="{{ asset('images/button.png') }}" alt="Login button" style="width: 240px; height: 50px;">
                    <span class="absolute left-1/2 -translate-x-1/2 -translate-y-1/2"
                        style="color: white; font-family: 'Press Start 2P', cursive; text-shadow: 2px 2px 4px #000000; font-size: 13px; top: 45%; white-space: nowrap;">{{ __('Login') }}</span>
                </button>
            </div>

            <div class="text-center text-sm text-gray-600 dark:text-gray-400 mt-4">
                ¿Necesitas una cuenta? <a href="{{ route('register') }}"
                    class="underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">Regístrate</a>
            </div>

            <div class="flex justify-center mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        href="{{ route('password.request') }}">
                        {{ __('he olvidado mi contraseña') }}
                    </a>
                @endif
            </div>
        </form>
    </x-guest-layout>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePasswordContainer = document.querySelector('.absolute.inset-y-0.right-0.pr-3.flex.items-center.text-sm.leading-5');
        const password = document.getElementById('password');
        const eyeOpened = document.getElementById('eyeOpened');
        const eyeClosed = document.getElementById('eyeClosed');

        if (togglePasswordContainer) {
            togglePasswordContainer.addEventListener('click', function (e) {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                eyeOpened.classList.toggle('hidden');
                eyeClosed.classList.toggle('hidden');
            });
        }
    });
</script>
</body>

</html>
