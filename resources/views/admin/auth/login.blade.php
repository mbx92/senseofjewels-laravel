<x-guest-layout>
    <div class="space-y-2">
        <div class="badge badge-primary badge-outline">CMS Access</div>
        <h1 class="text-2xl font-semibold">Admin Login</h1>
        <p class="text-sm text-base-content/70">Use your authorized admin account to access the company profile CMS and commerce dashboard.</p>
    </div>

    <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-4">
        @csrf

        <label class="form-control w-full">
            <div class="label">
                <span class="label-text">Email</span>
            </div>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="input input-bordered w-full" />
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
            <input type="password" name="password" required autocomplete="current-password" class="input input-bordered w-full" />
        </label>

        <label class="label cursor-pointer justify-start gap-3">
            <input type="checkbox" class="checkbox checkbox-sm" name="remember">
            <span class="label-text">Remember me</span>
        </label>

        <div class="card-actions justify-end pt-2">
            <button type="submit" class="btn btn-primary">Sign in to Admin</button>
        </div>
    </form>
</x-guest-layout>
