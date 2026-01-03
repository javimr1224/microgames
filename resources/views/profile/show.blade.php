<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - {{ $user->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="bg-gray-900 text-white font-sans flex flex-col min-h-screen">
    <div class="flex-grow">
        <x-header />

        <main class="max-w-7xl mx-auto mt-12 py-12 px-4">
            @if (session('status') === 'profile-updated')
                <div class="mb-4 bg-green-500 text-white p-4 rounded-lg">
                    Perfil actualizado correctamente.
                </div>
            @endif
            <!-- Profile Header -->
            <div class="bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="h-48 bg-gray-700 bg-cover bg-center" style="background-image: url('{{ $user->banner ? asset($user->banner) : 'https://via.placeholder.com/1500x500' }}');">
                </div>

                <div class="p-6">
                    <div class="flex items-end -mt-24">
                        <img src="{{ $user->avatar ? asset($user->avatar) : 'https://via.placeholder.com/150' }}" alt="Avatar" class="w-32 h-32 rounded-full border-4 border-gray-900 object-cover">
                        <div class="ml-4 flex-grow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h1 class="text-4xl font-bold" style="font-family: 'Press Start 2P', cursive;">{{ $user->name }}</h1>
                                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                                </div>
                                @if(Auth::user()->id === $user->id)
                                    <button id="edit-profile-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                                        Editar perfil
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-700 pt-6">
                        <h2 class="text-xl font-semibold" style="font-family: 'Press Start 2P', cursive;">Bio</h2>
                        <p class="mt-2 text-gray-400">{{ $user->bio ?? 'Este usuario no tiene biografía.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Purchased Games -->
            <div class="mt-12">
                <h2 class="text-3xl font-bold text-center mb-8" style="font-family: 'Press Start 2P', cursive;">Mis juegos</h2>
                @if($purchased_games->count() > 0)
                    <div id="games-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        @foreach($purchased_games as $game)
                            <div
                                class="relative group game-card cursor-pointer transition-all duration-300 hover:scale-105 border-2 border-gray-600 bg-gray-900/80 backdrop-blur-sm hover:border-white rounded-lg overflow-hidden shadow-lg">
                                <div class="p-6">
                                    <div class="relative z-10">
                                        <a href="{{ route('games.show', $game) }}">
                                            <img class="w-full h-40 object-cover mb-4 rounded-lg game-card-image"
                                                src="{{ $game->image }}" alt="{{ $game->name }}">
                                            <img src="{{ $game->video ?: asset('videos/may-sitting-near-waterfall-pokemon-emerald-pixel-wallpaperwaifu-com-ezgif.com-video-to-gif-converter.gif') }}" alt="Game GIF"
                                                class="w-full h-40 object-cover mb-4 rounded-lg game-card-video hidden">
                                        </a>
                                        <h3 class="text-2xl tracking-wider text-white mb-2 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-cyan-400 group-hover:to-pink-400 group-hover:bg-clip-text transition-all duration-300"
                                            style="font-family: 'Press Start 2P', monospace;">
                                            {{ $game->name }}
                                        </h3>
                                        <p class="text-gray-300 text-sm mb-4" style="font-family: 'Press Start 2P', monospace;">
                                            {{ $game->description }}
                                        </p>
                                        <a href="{{ route('game.launch', $game) }}"
                                            class="w-full inline-block text-center tracking-wider bg-gradient-to-r from-green-500 to-emerald-600 hover:shadow-lg hover:shadow-current/50 transition-all duration-300 border-0 py-2 px-4 rounded-md"
                                            style="font-family: 'Press Start 2P', monospace;">
                                            JUGAR
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center bg-gray-800 p-12 rounded-lg">
                        <p class="text-2xl text-gray-400">No has comprado ningún juego todavía.</p>
                        <a href="{{ route('storeGames') }}" class="inline-block mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg" style="font-family: 'Press Start 2P', cursive;">
                            Ir la la tienda
                        </a>
                    </div>
                @endif
            </div>
        </main>
    </div>
    <x-footer />

    <!-- Edit Profile Modal -->
    <div id="edit-profile-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-gray-800 rounded-lg p-8 w-full max-w-2xl">
            <h2 class="text-2xl font-bold mb-4" style="font-family: 'Press Start 2P', cursive;">Editar Perfil</h2>
            <form action="{{ route('profile.updateProfile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="bio" class="block text-sm font-medium text-gray-400 mb-2">Bio</label>
                    <textarea name="bio" id="bio" rows="4" class="w-full bg-gray-700 text-white rounded-lg p-2">{{ $user->bio }}</textarea>
                </div>
                <div class="mb-4">
                    <label for="avatar" class="block text-sm font-medium text-gray-400 mb-2">Avatar</label>
                    <input type="file" name="avatar" id="avatar" class="w-full bg-gray-700 text-white rounded-lg p-2">
                </div>
                <div class="mb-4">
                    <label for="banner" class="block text-sm font-medium text-gray-400 mb-2">Banner</label>
                    <input type="file" name="banner" id="banner" class="w-full bg-gray-700 text-white rounded-lg p-2">
                </div>
                <div class="flex justify-end">
                    <button type="button" id="cancel-btn" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg mr-2">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const editProfileBtn = document.getElementById('edit-profile-btn');
        const editProfileModal = document.getElementById('edit-profile-modal');
        const cancelBtn = document.getElementById('cancel-btn');

        if (editProfileBtn) {
            editProfileBtn.addEventListener('click', () => {
                editProfileModal.classList.remove('hidden');
            });
        }

        cancelBtn.addEventListener('click', () => {
            editProfileModal.classList.add('hidden');
        });

        window.addEventListener('click', (event) => {
            if (event.target === editProfileModal) {
                editProfileModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
