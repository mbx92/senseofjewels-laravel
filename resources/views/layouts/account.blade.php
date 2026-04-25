@extends('layouts.app')

@section('content')
<div class="mx-auto w-full max-w-[1480px] px-4 sm:px-5 lg:px-6 xl:px-8 py-12 lg:py-16">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8 xl:gap-10">

        {{-- ====== SIDEBAR ====== --}}
        <aside class="lg:col-span-4 lg:sticky lg:top-28 lg:self-start">
            <div class="space-y-7 rounded-[24px] border border-base-300 bg-base-100/90 p-5 lg:p-6">

                {{-- User Info --}}
                @auth
                <div class="pb-7 border-b border-base-300">
                    <p class="text-[11px] uppercase tracking-[0.3em] text-base-content/40 mb-3">{{ __('My Account') }}</p>
                    <p class="text-xl font-medium text-base-content leading-snug">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-base-content/45 mt-1.5 truncate">{{ auth()->user()->email }}</p>
                </div>
                @endauth

                {{-- Navigation --}}
                <nav class="flex flex-row gap-0 overflow-x-auto no-scrollbar -mx-4 px-4 border-b border-base-300 sm:mx-0 sm:px-0 lg:flex-col lg:border-b-0">
                    <a href="{{ route('profile.edit') }}"
                       class="shrink-0 flex items-center gap-3 py-3.5 pr-8 text-sm uppercase tracking-[0.2em] font-semibold lg:border-b lg:border-base-200 lg:pr-0 lg:py-4 transition-colors
                              {{ request()->routeIs('profile.edit') ? 'text-primary' : 'text-base-content/50 hover:text-base-content' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="shrink-0">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        {{ __('Profile') }}
                    </a>
                    <a href="{{ route('orders.index') }}"
                       class="shrink-0 flex items-center gap-3 py-3.5 pr-8 text-sm uppercase tracking-[0.2em] font-semibold lg:border-b lg:border-base-200 lg:pr-0 lg:py-4 transition-colors
                              {{ request()->routeIs('orders.index', 'orders.show') ? 'text-primary' : 'text-base-content/50 hover:text-base-content' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="shrink-0">
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <rect x="8" y="2" width="8" height="4" rx="1"/>
                            <path d="M9 12h6M9 16h4"/>
                        </svg>
                        {{ __('Orders') }}
                    </a>
                    <a href="{{ route('account.tracking') }}"
                       class="shrink-0 flex items-center gap-3 py-3.5 pr-8 text-sm uppercase tracking-[0.2em] font-semibold lg:border-b lg:border-base-200 lg:pr-0 lg:py-4 transition-colors
                              {{ request()->routeIs('account.tracking') ? 'text-primary' : 'text-base-content/50 hover:text-base-content' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="shrink-0">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        {{ __('Tracking') }}
                    </a>
                    <a href="{{ route('account.reviews') }}"
                       class="shrink-0 flex items-center gap-3 py-3.5 pr-8 text-sm uppercase tracking-[0.2em] font-semibold lg:border-b lg:border-base-200 lg:pr-0 lg:py-4 transition-colors
                              {{ request()->routeIs('account.reviews') ? 'text-primary' : 'text-base-content/50 hover:text-base-content' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="shrink-0">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        {{ __('Reviews') }}
                    </a>
                </nav>

                {{-- Sign Out --}}
                @auth
                <form method="POST" action="{{ route('logout') }}" class="hidden lg:block pt-2">
                    @csrf
                    <button type="submit"
                            class="text-xs uppercase tracking-[0.22em] text-base-content/30 hover:text-error/70 transition-colors">
                        {{ __('Sign Out') }}
                    </button>
                </form>
                @endauth

            </div>
        </aside>

        {{-- ====== MAIN CONTENT ====== --}}
        <section class="min-w-0 rounded-[24px] border border-base-300 bg-base-100/90 p-5 shadow-sm sm:p-6 lg:col-span-8 lg:p-7 xl:p-8">
            @yield('account-content')
        </section>

    </div>
</div>
@endsection
