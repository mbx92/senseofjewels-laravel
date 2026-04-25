@props([
    'disabled' => false,
    'title' => 'Fitur ini tidak aktif',
    'description' => 'Silakan aktifkan di halaman Settings > Commerce',
    'fullscreen' => false,
    'dashboardUrl' => null,
])

<div class="relative">
    <div @class([$disabled ? 'pointer-events-none select-none' : ''])>
        {{ $slot }}
    </div>

    @if($disabled)
        <div @class([
            'z-[70] bg-black',
            'fixed inset-0' => $fullscreen,
            'absolute inset-0' => !$fullscreen,
        ])></div>
        <div @class([
            'z-[71] flex items-center justify-center',
            'fixed inset-0' => $fullscreen,
            'absolute inset-0' => !$fullscreen,
        ]) style="transform: translateY(-10px);">
            <div class="max-w-md rounded-[16px] border border-base-300 bg-base-100 px-5 py-4 text-center shadow-sm">
                <div class="mx-auto mb-2 flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-base-300 bg-base-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 10-8 0v4m-2 0h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-base-content">{{ $title }}</p>
                <p class="mt-1 text-xs text-base-content/60">{{ $description }}</p>
                <a href="{{ $dashboardUrl ?? route('admin.dashboard') }}"
                   class="mt-3 inline-flex items-center justify-center border border-base-content/20 px-4 py-2 text-[10px] uppercase tracking-[0.2em] text-base-content/70 hover:text-base-content hover:border-base-content/40 transition-colors">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
