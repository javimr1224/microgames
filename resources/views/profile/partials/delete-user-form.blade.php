<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Eliminar Cuenta
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente.
        </p>
    </header>

    <x-primary-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Eliminar Cuenta</x-primary-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                ¿Estás seguro de que quieres eliminar tu cuenta?
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. Por favor, introduce tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Contraseña" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Contraseña"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-primary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-primary-button>

                <x-primary-button class="ms-3">
                    Eliminar Cuenta
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</section>
