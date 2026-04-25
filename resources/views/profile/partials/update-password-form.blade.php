<section>
    <header>
        <h2 class="text-lg font-medium text-base-content">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-base-content/60">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                   class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                   class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="text-[11px] uppercase tracking-widest bg-neutral text-neutral-content px-6 py-2.5 hover:bg-neutral/80 transition-colors">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-base-content/55"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
