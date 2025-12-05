<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Regístrate</title>
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
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="flex justify-center">
                <h1 class="p-6 text-[25px] text-[#264653] text-nowrap" style="font-family: 'Press Start 2P', cursive; text-shadow: 1px 1px 4px #00000061;">Regístrate</h1>
            </div>

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                    required autofocus autocomplete="name" placeholder="nombre de usuario" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autocomplete="username" placeholder="correo electrónico" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('')" />

                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="new-password" placeholder="contraseña" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('')" />

                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                    name="password_confirmation" required autocomplete="new-password" placeholder="confirmar contraseña" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex flex-col items-center justify-end mt-4">
                <div class="flex justify-center mt-4">
                    <button type="submit" class="relative animatable-button">
                        <img src="{{ asset('images/button.png') }}" alt="Register button" style="width: 280px; height: 50px;">
                        <span class="absolute left-1/2 -translate-x-1/2 -translate-y-1/2"
                            style="color: white; font-family: 'Press Start 2P', cursive; text-shadow: 2px 2px 4px #000000; font-size: 13px; top: 45%; white-space: nowrap;">{{ __('Registrate gratis') }}</span>
                    </button>
                </div>
                    <a class="mt-6 underline text-sm text-gray-900 dark:text-gray-900 hover:text-gray-900 dark:hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-900 dark:focus:ring-offset-black-900"
                    href="{{ route('login') }}"> {{ __('Ya tienes una cuenta?') }}
                </a>
            </div>
        </form>
    </x-guest-layout>

</body>

</html>
