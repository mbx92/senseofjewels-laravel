<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Admin Panel' }} - {{ config('app.name', 'Sense of Jewels') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="drawer lg:drawer-open">
            <input id="admin-drawer" type="checkbox" class="drawer-toggle" />

            <div class="drawer-content flex min-h-screen flex-col bg-base-200">
                <div class="navbar border-b border-base-300 bg-base-100 px-4 shadow-sm">
                    <div class="flex-none lg:hidden">
                        <label for="admin-drawer" class="btn btn-square btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </label>
                    </div>
                    <div class="flex-1">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost text-lg font-semibold">
                            CMS Admin Panel
                        </a>
                    </div>
                    <div class="flex-none gap-2">
                        <div class="badge badge-primary badge-outline">corporate</div>
                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-outline btn-sm">
                                {{ auth()->user()->name ?? 'Admin' }}
                            </label>
                            <ul tabindex="0" class="menu dropdown-content z-[1] mt-3 w-56 rounded-box border border-base-300 bg-base-100 p-2 shadow">
                                <li><a href="{{ route('home') }}">Open Website</a></li>
                                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <main class="flex-1 p-4 sm:p-6">
                    @if (session('status'))
                        <div role="alert" class="alert alert-success mb-6">
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div role="alert" class="alert alert-error mb-6">
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>

            <div class="drawer-side z-40">
                <label for="admin-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                <aside class="min-h-full w-80 border-r border-base-300 bg-base-100 p-4">
                    <div class="rounded-box bg-base-200 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-primary">Sense of Jewels</p>
                        <h2 class="mt-2 text-xl font-semibold">Admin Modules</h2>
                        <p class="mt-2 text-sm text-base-content/70">
                            CMS, catalog, orders, inventory, and role management scaffolding lives here.
                        </p>
                    </div>

                    <ul class="menu mt-4 gap-1 rounded-box bg-base-100 p-2">
                        <li><a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="menu-title"><span>Content</span></li>
                        <li><span class="cursor-default text-base-content/70">Pages & Sections</span></li>
                        <li><span class="cursor-default text-base-content/70">Services</span></li>
                        <li><span class="cursor-default text-base-content/70">Portfolio</span></li>
                        <li><span class="cursor-default text-base-content/70">Testimonials</span></li>
                        <li class="menu-title"><span>Commerce</span></li>
                        <li><span class="cursor-default text-base-content/70">Products & Categories</span></li>
                        <li><span class="cursor-default text-base-content/70">Orders</span></li>
                        <li><span class="cursor-default text-base-content/70">Inventory Logs</span></li>
                        <li><span class="cursor-default text-base-content/70">Discounts & Vouchers</span></li>
                        <li class="menu-title"><span>System</span></li>
                        <li><span class="cursor-default text-base-content/70">Users & Roles</span></li>
                        <li><span class="cursor-default text-base-content/70">Settings</span></li>
                    </ul>
                </aside>
            </div>
        </div>
    </body>
</html>
