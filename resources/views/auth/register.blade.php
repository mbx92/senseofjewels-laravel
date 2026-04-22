<x-guest-layout>
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold">Create Account</h1>
        <p class="text-sm text-base-content/70">Register to manage your orders, checkout faster, and save your contact details.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <label class="form-control w-full">
            <div class="label">
                <span class="label-text">Full name</span>
            </div>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="input input-bordered w-full" />
            @error('name')
                <div class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </div>
            @enderror
        </label>

        <label class="form-control w-full">
            <div class="label">
                <span class="label-text">Email</span>
            </div>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="input input-bordered w-full" />
            @error('email')
                <div class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </div>
            @enderror
        </label>

        <label class="form-control w-full">
            <div class="label">
                <span class="label-text">Password</span>
            </div>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="input input-bordered w-full" />
            @error('password')
                <div class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </div>
            @enderror
        </label>

        <label class="form-control w-full">
            <div class="label">
                <span class="label-text">Confirm password</span>
            </div>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="input input-bordered w-full" />
        </label>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
            <a class="link link-hover text-sm" href="{{ route('login') }}">
                Already registered?
            </a>

            <button type="submit" class="btn btn-primary">
                Register
            </button>
        </div>
    </form>
</x-guest-layout>
