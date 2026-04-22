<x-guest-layout>
    <div class="space-y-2">
        <h1 class="text-2xl font-semibold">Customer Login</h1>
        <p class="text-sm text-base-content/70">Access your profile, checkout flow, and order tracking in one place.</p>
    </div>

    @if (session('status'))
        <div role="alert" class="alert alert-success mt-4">
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <label class="form-control w-full">
            <div class="label">
                <span class="label-text">Email</span>
            </div>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="input input-bordered w-full" />
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
            <input id="password" type="password" name="password" required autocomplete="current-password" class="input input-bordered w-full" />
            @error('password')
                <div class="label">
                    <span class="label-text-alt text-error">{{ $message }}</span>
                </div>
            @enderror
        </label>

        <label class="label cursor-pointer justify-start gap-3">
            <input id="remember_me" type="checkbox" class="checkbox checkbox-sm" name="remember">
            <span class="label-text">Remember me</span>
        </label>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
            @if (Route::has('password.request'))
                <a class="link link-hover text-sm" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif

            <button type="submit" class="btn btn-primary">
                Log in
            </button>
        </div>
    </form>

    <p class="mt-4 text-sm text-base-content/70">
        New customer?
        <a href="{{ route('register') }}" class="link link-primary">Create an account</a>
    </p>
</x-guest-layout>
