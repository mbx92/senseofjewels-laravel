<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-base-content">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-base-content/60">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="text-[11px] uppercase tracking-widest bg-error text-error-content px-6 py-2.5 hover:bg-error/90 transition-colors"
    >{{ __('Delete Account') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-base-content">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-base-content/60">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <label for="password" class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">{{ __('Password') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')"
                        class="text-[10px] uppercase tracking-[0.2em] text-base-content/60 hover:text-base-content transition-colors">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="ms-3 text-[11px] uppercase tracking-widest bg-error text-error-content px-5 py-2.5 hover:bg-error/90 transition-colors">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
