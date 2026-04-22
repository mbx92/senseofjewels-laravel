<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">Customer Dashboard</h2>
                <p class="text-sm text-base-content/70">Monitor your orders, profile, and shopping activity.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm">Continue Shopping</a>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="stat rounded-box border border-base-300 bg-base-100 shadow-sm">
            <div class="stat-title">Account Status</div>
            <div class="stat-value text-primary">Active</div>
            <div class="stat-desc">Authenticated via Laravel Breeze</div>
        </div>

        <div class="stat rounded-box border border-base-300 bg-base-100 shadow-sm">
            <div class="stat-title">Next Step</div>
            <div class="stat-value text-secondary">Checkout</div>
            <div class="stat-desc">Use the session cart and checkout wizard</div>
        </div>

        <div class="stat rounded-box border border-base-300 bg-base-100 shadow-sm">
            <div class="stat-title">Tracking</div>
            <div class="stat-value text-accent">Ready</div>
            <div class="stat-desc">Public order tracking route is scaffolded</div>
        </div>

        <div class="card border border-base-300 bg-base-100 shadow-sm lg:col-span-2">
            <div class="card-body">
                <h3 class="card-title">Your storefront foundation is ready</h3>
                <p class="text-base-content/70">
                    Authentication, shop catalog, cart, checkout skeleton, and order tracking are already wired into the Laravel app.
                </p>
                <div class="card-actions justify-end">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline">Open Cart</a>
                    <a href="{{ route('shop.index') }}" class="btn btn-primary">Visit Shop</a>
                </div>
            </div>
        </div>

        <div class="card border border-base-300 bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="card-title">Need admin access?</h3>
                <p class="text-sm text-base-content/70">
                    Users with the <code>admin</code> or <code>super-admin</code> role can sign in via the dedicated CMS route.
                </p>
                <div class="card-actions justify-end">
                    <a href="{{ route('admin.login') }}" class="btn btn-ghost">Admin Login</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
