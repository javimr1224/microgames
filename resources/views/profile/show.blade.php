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
                    Profile updated successfully.
                </div>
            @endif
            <!-- Profile Header -->
            <div class="bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="h-48 bg-gray-700 bg-cover bg-center" style="background-image: url('{{ $user->banner ? asset('storage/' . $user->banner) : 'https://via.placeholder.com/1500x500' }}');">
                </div>

                <div class="p-6">
                    <div class="flex items-end -mt-24">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://via.placeholder.com/150' }}" alt="Avatar" class="w-32 h-32 rounded-full border-4 border-gray-900 object-cover">
                        <div class="ml-4 flex-grow">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h1 class="text-4xl font-bold" style="font-family: 'Press Start 2P', cursive;">{{ $user->name }}</h1>
                                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                                </div>
                                @if(Auth::user()->id === $user->id)
                                    <button id="edit-profile-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                                        Edit Profile
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-700 pt-6">
                        <h2 class="text-xl font-semibold" style="font-family: 'Press Start 2P', cursive;">Bio</h2>
                        <p class="mt-2 text-gray-400">{{ $user->bio ?? 'This user has no bio yet.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Purchased Games -->
            <div class="mt-12">
                <h2 class="text-3xl font-bold text-center mb-8" style="font-family: 'Press Start 2P', cursive;">My Games</h2>
                @if($purchased_games->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        @foreach($purchased_games as $game)
                            <a href="{{ route('games.show', $game) }}" class="bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                                <img src="{{ asset($game->image) }}" alt="{{ $game->name }}" class="w-full h-48 object-cover rounded-t-lg">
                                <div class="p-4">
                                    <h3 class="text-lg font-bold" style="font-family: 'Press Start 2P', cursive;">{{ $game->name }}</h3>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center bg-gray-800 p-12 rounded-lg">
                        <p class="text-2xl text-gray-400">You haven't purchased any games yet.</p>
                        <a href="{{ route('storeGames') }}" class="inline-block mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg" style="font-family: 'Press Start 2P', cursive;">
                            Go to Store
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
            <h2 class="text-2xl font-bold mb-4" style="font-family: 'Press Start 2P', cursive;">Edit Profile</h2>
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
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                        Save Changes
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

        // Close modal if clicking outside of it
        window.addEventListener('click', (event) => {
            if (event.target === editProfileModal) {
                editProfileModal.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
