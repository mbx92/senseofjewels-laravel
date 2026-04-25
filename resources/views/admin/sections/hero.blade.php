@extends('layouts.admin')

@section('page-title', 'Hero Section')

@section('content')
@php
    $s  = $section?->settings ?? [];
    $sv = fn(string $key, string $default = '') => old($key, $s[$key] ?? $default);
@endphp

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold">Hero Section</h1>
        <p class="text-sm text-base-content/60">Edit 3-panel hero pada halaman depan.</p>
    </div>
    <a href="{{ route('home') }}" target="_blank" class="btn btn-ghost btn-sm gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        Preview Landing Page
    </a>
</div>

<form action="{{ route('admin.hero.update') }}" method="POST" class="pb-24">
    @csrf
    @method('PUT')

    {{-- ── PANEL 1 — Campaign Hero ── --}}
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body space-y-5">
            <div class="flex items-center gap-3 border-b border-base-300 pb-4">
                <div class="badge badge-neutral text-xs">Panel 1</div>
                <h2 class="font-bold text-lg">Campaign Hero <span class="text-base-content/40 font-normal text-sm">(kiri, besar)</span></h2>
            </div>

            @php
                $posBtns = [
                    ['top-left', 'up-left'],
                    ['top-center', 'up'],
                    ['top-right', 'up-right'],
                    ['middle-left', 'left'],
                    ['middle-center', 'center'],
                    ['middle-right', 'right'],
                    ['bottom-left', 'down-left'],
                    ['bottom-center', 'down'],
                    ['bottom-right', 'down-right'],
                ];
            @endphp
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Season Badge</span><span class="label-text-alt text-base-content/40">e.g. NEW SEASON 2026</span></label>
                    <input type="text" name="season_badge" value="{{ $sv('season_badge') }}"
                        class="input input-bordered w-full input-sm" placeholder="NEW SEASON {{ date('Y') }}">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Eyebrow Text</span><span class="label-text-alt text-base-content/40">e.g. ARTISAN JEWELRY · BALI</span></label>
                    <input type="text" name="eyebrow" value="{{ $sv('eyebrow') }}"
                        class="input input-bordered w-full input-sm" placeholder="ARTISAN JEWELRY · BALI">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control" x-data="{ len: {{ strlen(old('title', $section?->title ?? '')) }} }">
                    <label class="label">
                        <span class="label-text font-medium">Judul (baris 1)</span>
                        <span class="label-text-alt" :class="len > 35 ? 'text-warning' : 'text-base-content/40'">
                            <span x-text="len">{{ strlen(old('title', $section?->title ?? '')) }}</span>/40
                        </span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $section?->title) }}"
                        maxlength="40"
                        @input="len = $event.target.value.length"
                        class="input input-bordered w-full @error('title') input-error @enderror"
                        placeholder="Timeless" required>
                    @error('title')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
                <div class="form-control" x-data="{ len: {{ strlen(old('subtitle', $section?->subtitle ?? '')) }} }">
                    <label class="label">
                        <span class="label-text font-medium">Tagline (baris 2)</span>
                        <span class="label-text-alt" :class="len > 35 ? 'text-warning' : 'text-base-content/40'">
                            <span x-text="len">{{ strlen(old('subtitle', $section?->subtitle ?? '')) }}</span>/40 · italic
                        </span>
                    </label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $section?->subtitle) }}"
                        maxlength="40"
                        @input="len = $event.target.value.length"
                        class="input input-bordered w-full" placeholder="Elegance">
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Deskripsi Singkat</span></label>
                <textarea name="description" rows="2" class="textarea textarea-bordered w-full"
                    placeholder="Handcrafted fine jewelry designed for the modern everyday.">{{ old('description', $section?->content) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Teks Tombol CTA</span></label>
                    <input type="text" name="cta_text" value="{{ old('cta_text', $section?->cta_text) }}"
                        class="input input-bordered w-full" placeholder="SHOP COLLECTION">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Link CTA</span></label>
                    <input type="text" name="cta_url" value="{{ old('cta_url', $section?->cta_url) }}"
                        class="input input-bordered w-full" placeholder="/shop">
                </div>
            </div>

            <div class="form-control" x-data="{ pos: '{{ $sv('text_position', 'top-left') }}' }">
                <label class="label"><span class="label-text font-medium">Posisi Teks</span><span class="label-text-alt text-base-content/40">klik untuk pilih posisi</span></label>
                <input type="hidden" name="text_position" :value="pos">
                <div style="display:inline-grid;grid-template-columns:repeat(3,34px);gap:3px;">
                    @foreach($posBtns as [$p, $direction])
                    <button type="button" @click="pos='{{ $p }}'"
                            :class="pos === '{{ $p }}' ? 'btn-primary' : 'btn-ghost'"
                            class="btn btn-xs" style="width:34px;height:34px;min-height:0;padding:0;"
                            title="{{ $p }}">
                        @if($direction === 'center')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <circle cx="10" cy="10" r="2.5" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                @if($direction === 'up-left')
                                    <path fill-rule="evenodd" d="M14.78 14.78a.75.75 0 0 1-1.06 0L6 7v5.25a.75.75 0 0 1-1.5 0V5.19A1.19 1.19 0 0 1 5.69 4h7.06a.75.75 0 0 1 0 1.5H7l7.78 7.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                @elseif($direction === 'up')
                                    <path fill-rule="evenodd" d="M10 16a.75.75 0 0 1-.75-.75V6.81L6.28 9.78a.75.75 0 1 1-1.06-1.06l4.25-4.25a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 1 1-1.06 1.06L10.75 6.8v8.44A.75.75 0 0 1 10 16Z" clip-rule="evenodd" />
                                @elseif($direction === 'up-right')
                                    <path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 0 1 0-1.06L13 6h-5.25a.75.75 0 0 1 0-1.5h7.06A1.19 1.19 0 0 1 16 5.69v7.06a.75.75 0 0 1-1.5 0V7l-7.72 7.78a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                                @elseif($direction === 'left')
                                    <path fill-rule="evenodd" d="M16 10a.75.75 0 0 1-.75.75H6.81l2.97 2.97a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 1.06L6.8 9.25h8.44A.75.75 0 0 1 16 10Z" clip-rule="evenodd" />
                                @elseif($direction === 'right')
                                    <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h8.44L10.22 6.28a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06l2.97-2.97H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                @elseif($direction === 'down-left')
                                    <path fill-rule="evenodd" d="M14.78 5.22a.75.75 0 0 1 0 1.06L7 14h5.25a.75.75 0 0 1 0 1.5H5.19A1.19 1.19 0 0 1 4 14.31V7.25a.75.75 0 0 1 1.5 0V13l7.72-7.78a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                                @elseif($direction === 'down')
                                    <path fill-rule="evenodd" d="M10 4a.75.75 0 0 1 .75.75v8.44l2.97-2.97a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 1 1 1.06-1.06l2.97 2.97V4.75A.75.75 0 0 1 10 4Z" clip-rule="evenodd" />
                                @elseif($direction === 'down-right')
                                    <path fill-rule="evenodd" d="M5.22 5.22a.75.75 0 0 1 1.06 0L14 13V7.75a.75.75 0 0 1 1.5 0v7.06A1.19 1.19 0 0 1 14.31 16H7.25a.75.75 0 0 1 0-1.5H13L5.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                @endif
                            </svg>
                        @endif
                    </button>
                    @endforeach
                </div>
                <span class="text-xs text-base-content/40 mt-1" x-text="pos.replace('-',' · ')"></span>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Background Images (Carousel)</span>
                    <span class="label-text-alt text-base-content/40">Tambah lebih dari 1 untuk carousel otomatis (5 detik)</span>
                </label>
                @php
                    $existingHeroImages = $section?->settings['hero_images'] ?? [];
                    if (empty($existingHeroImages) && $section?->image_url) {
                        $existingHeroImages = [$section->image_url];
                    }
                @endphp
                <div x-data="heroCarouselPicker(@js($existingHeroImages))">
                    <input type="hidden" name="hero_images" x-ref="jsonInput" x-effect="$refs.jsonInput.value = JSON.stringify(images)">

                    {{-- Thumbnails --}}
                    <div class="flex flex-wrap gap-2 mb-3">
                        <template x-for="(img, idx) in images" :key="img">
                            <div class="relative w-20 h-20 bg-base-200 border border-base-300 overflow-hidden group/thumb shrink-0">
                                <img :src="img" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/0 group-hover/thumb:bg-black/40 transition-colors flex items-center justify-center">
                                    <button type="button" @click="remove(idx)"
                                            class="opacity-0 group-hover/thumb:opacity-100 text-white text-sm leading-none w-5 h-5 flex items-center justify-center bg-error rounded-full transition-opacity">&times;</button>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-center py-0.5" style="font-size:8px" x-text="idx === 0 ? 'Utama' : 'Slide '+(idx+1)"></div>
                            </div>
                        </template>
                        <template x-if="images.length === 0">
                            <div class="w-20 h-20 bg-base-200 border border-dashed border-base-300 flex items-center justify-center">
                                <span class="text-base-content/30 uppercase text-center px-1" style="font-size:9px">No Image</span>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="open = true"
                            class="text-[10px] uppercase tracking-widest border border-base-content/30 px-3 py-1.5 hover:bg-base-content hover:text-base-100 transition-colors">
                        + Tambah Gambar
                    </button>

                    {{-- Modal --}}
                    <div x-show="open" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70">
                        <div class="bg-base-100 w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl">
                            <div class="flex items-center justify-between px-6 py-4 border-b border-base-300 shrink-0">
                                <h3 class="display-font text-2xl">Media Library</h3>
                                <div class="flex items-center gap-3">
                                    <input type="text" x-model.debounce.400ms="query" placeholder="Search..."
                                           class="border-b border-base-content/20 bg-transparent py-1.5 text-xs w-40 focus:outline-none focus:border-primary">
                                    <button @click="open = false" class="text-base-content/40 hover:text-base-content text-xl leading-none">&times;</button>
                                </div>
                            </div>
                            <div class="overflow-y-auto p-4 flex-1">
                                <template x-if="loading">
                                    <div class="py-12 text-center text-sm text-base-content/40">Loading…</div>
                                </template>
                                <template x-if="!loading && mediaItems.length === 0">
                                    <div class="py-12 text-center text-sm text-base-content/40">No media found.</div>
                                </template>
                                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                                    <template x-for="item in mediaItems" :key="item.id">
                                        <button type="button" @click="pick(item)"
                                                :class="images.includes(item.url) ? 'ring-2 ring-primary ring-offset-1' : 'hover:opacity-80'"
                                                class="aspect-square bg-base-200 overflow-hidden relative transition-all">
                                            <template x-if="item.is_image">
                                                <img :src="item.url" :alt="item.alt" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="images.includes(item.url)">
                                                <div class="absolute inset-0 flex items-center justify-center bg-primary/20">
                                                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                </div>
                                            </template>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div class="px-6 py-3 border-t border-base-300 flex items-center justify-between shrink-0">
                                <span class="text-[10px] text-base-content/50" x-text="images.length + ' gambar dipilih'"></span>
                                <button type="button" @click="open = false"
                                        class="btn btn-sm btn-neutral">Selesai</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PANEL 2 — Product Banner ── --}}
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body space-y-5">
            <div class="flex items-center gap-3 border-b border-base-300 pb-4">
                <div class="badge badge-primary text-xs">Panel 2</div>
                <h2 class="font-bold text-lg">Product Banner <span class="text-base-content/40 font-normal text-sm">(kanan atas)</span></h2>
            </div>

            <div class="form-control" x-data="{ pos: '{{ $sv('banner1_text_position', 'bottom-left') }}' }">
                <label class="label"><span class="label-text font-medium">Posisi Teks</span></label>
                <input type="hidden" name="banner1_text_position" :value="pos">
                <div style="display:inline-grid;grid-template-columns:repeat(3,34px);gap:3px;">
                    @foreach($posBtns as [$p, $direction])
                    <button type="button" @click="pos='{{ $p }}'"
                            :class="pos === '{{ $p }}' ? 'btn-primary' : 'btn-ghost'"
                            class="btn btn-xs" style="width:34px;height:34px;min-height:0;padding:0;"
                            title="{{ $p }}">
                        @if($direction === 'center')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <circle cx="10" cy="10" r="2.5" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                @if($direction === 'up-left')
                                    <path fill-rule="evenodd" d="M14.78 14.78a.75.75 0 0 1-1.06 0L6 7v5.25a.75.75 0 0 1-1.5 0V5.19A1.19 1.19 0 0 1 5.69 4h7.06a.75.75 0 0 1 0 1.5H7l7.78 7.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                @elseif($direction === 'up')
                                    <path fill-rule="evenodd" d="M10 16a.75.75 0 0 1-.75-.75V6.81L6.28 9.78a.75.75 0 1 1-1.06-1.06l4.25-4.25a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 1 1-1.06 1.06L10.75 6.8v8.44A.75.75 0 0 1 10 16Z" clip-rule="evenodd" />
                                @elseif($direction === 'up-right')
                                    <path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 0 1 0-1.06L13 6h-5.25a.75.75 0 0 1 0-1.5h7.06A1.19 1.19 0 0 1 16 5.69v7.06a.75.75 0 0 1-1.5 0V7l-7.72 7.78a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                                @elseif($direction === 'left')
                                    <path fill-rule="evenodd" d="M16 10a.75.75 0 0 1-.75.75H6.81l2.97 2.97a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 1.06L6.8 9.25h8.44A.75.75 0 0 1 16 10Z" clip-rule="evenodd" />
                                @elseif($direction === 'right')
                                    <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h8.44L10.22 6.28a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06l2.97-2.97H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                @elseif($direction === 'down-left')
                                    <path fill-rule="evenodd" d="M14.78 5.22a.75.75 0 0 1 0 1.06L7 14h5.25a.75.75 0 0 1 0 1.5H5.19A1.19 1.19 0 0 1 4 14.31V7.25a.75.75 0 0 1 1.5 0V13l7.72-7.78a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                                @elseif($direction === 'down')
                                    <path fill-rule="evenodd" d="M10 4a.75.75 0 0 1 .75.75v8.44l2.97-2.97a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 1 1 1.06-1.06l2.97 2.97V4.75A.75.75 0 0 1 10 4Z" clip-rule="evenodd" />
                                @elseif($direction === 'down-right')
                                    <path fill-rule="evenodd" d="M5.22 5.22a.75.75 0 0 1 1.06 0L14 13V7.75a.75.75 0 0 1 1.5 0v7.06A1.19 1.19 0 0 1 14.31 16H7.25a.75.75 0 0 1 0-1.5H13L5.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                @endif
                            </svg>
                        @endif
                    </button>
                    @endforeach
                </div>
                <span class="text-xs text-base-content/40 mt-1" x-text="pos.replace('-',' · ')"></span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Label</span><span class="label-text-alt text-base-content/40">uppercase kecil</span></label>
                    <input type="text" name="banner1_label" value="{{ $sv('banner1_label') }}"
                        class="input input-bordered w-full input-sm" placeholder="SELECTED COLLECTION">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Judul</span></label>
                    <input type="text" name="banner1_title" value="{{ $sv('banner1_title') }}"
                        class="input input-bordered w-full input-sm" placeholder="Emas &amp; Perak">
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Subtitle (italic gold)</span></label>
                <input type="text" name="banner1_subtitle" value="{{ $sv('banner1_subtitle') }}"
                    class="input input-bordered w-full" placeholder="Artisan Bali">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Teks Tombol</span><span class="label-text-alt text-base-content/40">kosongkan = hidden</span></label>
                    <input type="text" name="banner1_cta_text" value="{{ $sv('banner1_cta_text') }}"
                        class="input input-bordered w-full" placeholder="SHOP NOW">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Link Tombol</span></label>
                    <input type="text" name="banner1_cta_url" value="{{ $sv('banner1_cta_url') }}"
                        class="input input-bordered w-full" placeholder="/shop">
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Background Image</span></label>
                @include('admin.components.media-picker', [
                    'inputName'    => 'banner1_image',
                    'inputId'      => 'banner1_bg',
                    'currentValue' => $sv('banner1_image'),
                    'label'        => 'Product Banner Background',
                ])
            </div>
        </div>
    </div>

    {{-- ── PANEL 3 — Category Banner ── --}}
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body space-y-5">
            <div class="flex items-center gap-3 border-b border-base-300 pb-4">
                <div class="badge badge-secondary text-xs">Panel 3</div>
                <h2 class="font-bold text-lg">Category Banner <span class="text-base-content/40 font-normal text-sm">(kanan bawah)</span></h2>
            </div>

            <div class="form-control" x-data="{ pos: '{{ $sv('banner2_text_position', 'bottom-left') }}' }">
                <label class="label"><span class="label-text font-medium">Posisi Teks</span></label>
                <input type="hidden" name="banner2_text_position" :value="pos">
                <div style="display:inline-grid;grid-template-columns:repeat(3,34px);gap:3px;">
                    @foreach($posBtns as [$p, $direction])
                    <button type="button" @click="pos='{{ $p }}'"
                            :class="pos === '{{ $p }}' ? 'btn-primary' : 'btn-ghost'"
                            class="btn btn-xs" style="width:34px;height:34px;min-height:0;padding:0;"
                            title="{{ $p }}">
                        @if($direction === 'center')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <circle cx="10" cy="10" r="2.5" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                @if($direction === 'up-left')
                                    <path fill-rule="evenodd" d="M14.78 14.78a.75.75 0 0 1-1.06 0L6 7v5.25a.75.75 0 0 1-1.5 0V5.19A1.19 1.19 0 0 1 5.69 4h7.06a.75.75 0 0 1 0 1.5H7l7.78 7.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                @elseif($direction === 'up')
                                    <path fill-rule="evenodd" d="M10 16a.75.75 0 0 1-.75-.75V6.81L6.28 9.78a.75.75 0 1 1-1.06-1.06l4.25-4.25a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 1 1-1.06 1.06L10.75 6.8v8.44A.75.75 0 0 1 10 16Z" clip-rule="evenodd" />
                                @elseif($direction === 'up-right')
                                    <path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 0 1 0-1.06L13 6h-5.25a.75.75 0 0 1 0-1.5h7.06A1.19 1.19 0 0 1 16 5.69v7.06a.75.75 0 0 1-1.5 0V7l-7.72 7.78a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                                @elseif($direction === 'left')
                                    <path fill-rule="evenodd" d="M16 10a.75.75 0 0 1-.75.75H6.81l2.97 2.97a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 1.06L6.8 9.25h8.44A.75.75 0 0 1 16 10Z" clip-rule="evenodd" />
                                @elseif($direction === 'right')
                                    <path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h8.44L10.22 6.28a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06l2.97-2.97H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                @elseif($direction === 'down-left')
                                    <path fill-rule="evenodd" d="M14.78 5.22a.75.75 0 0 1 0 1.06L7 14h5.25a.75.75 0 0 1 0 1.5H5.19A1.19 1.19 0 0 1 4 14.31V7.25a.75.75 0 0 1 1.5 0V13l7.72-7.78a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                                @elseif($direction === 'down')
                                    <path fill-rule="evenodd" d="M10 4a.75.75 0 0 1 .75.75v8.44l2.97-2.97a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 1 1 1.06-1.06l2.97 2.97V4.75A.75.75 0 0 1 10 4Z" clip-rule="evenodd" />
                                @elseif($direction === 'down-right')
                                    <path fill-rule="evenodd" d="M5.22 5.22a.75.75 0 0 1 1.06 0L14 13V7.75a.75.75 0 0 1 1.5 0v7.06A1.19 1.19 0 0 1 14.31 16H7.25a.75.75 0 0 1 0-1.5H13L5.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                @endif
                            </svg>
                        @endif
                    </button>
                    @endforeach
                </div>
                <span class="text-xs text-base-content/40 mt-1" x-text="pos.replace('-',' · ')"></span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Label</span></label>
                    <input type="text" name="banner2_label" value="{{ $sv('banner2_label') }}"
                        class="input input-bordered w-full input-sm" placeholder="EST. 2019">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Judul (baris 1)</span></label>
                    <input type="text" name="banner2_title" value="{{ $sv('banner2_title') }}"
                        class="input input-bordered w-full input-sm" placeholder="Cincin &amp;">
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Tagline (italic gold, baris 2)</span></label>
                <input type="text" name="banner2_subtitle" value="{{ $sv('banner2_subtitle') }}"
                    class="input input-bordered w-full" placeholder="Kalung Pilihan">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Teks Tombol</span><span class="label-text-alt text-base-content/40">kosongkan = hidden</span></label>
                    <input type="text" name="banner2_cta_text" value="{{ $sv('banner2_cta_text') }}"
                        class="input input-bordered w-full" placeholder="SHOP NOW">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text font-medium">Link Tombol</span></label>
                    <input type="text" name="banner2_cta_url" value="{{ $sv('banner2_cta_url') }}"
                        class="input input-bordered w-full" placeholder="/shop/cincin">
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text font-medium">Background Image</span></label>
                @include('admin.components.media-picker', [
                    'inputName'    => 'banner2_image',
                    'inputId'      => 'banner2_bg',
                    'currentValue' => $sv('banner2_image'),
                    'label'        => 'Category Banner Background',
                ])
            </div>
        </div>
    </div>

    {{-- ── Settings & Save — sticky floating bar ── --}}
    <div class="sticky bottom-0 z-30 bg-base-100/95 backdrop-blur border-t border-base-300 shadow-[0_-4px_24px_rgba(0,0,0,0.08)] -mx-5 lg:-mx-8 px-5 lg:px-8"
         id="hero-save-bar">
        <div class="max-w-5xl mx-auto px-6 py-3">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <label class="label cursor-pointer justify-start gap-4">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="toggle toggle-primary"
                        {{ old('is_active', $section?->is_active ?? true) ? 'checked' : '' }}>
                    <span class="label-text font-medium">Aktifkan hero section</span>
                </label>
                <div class="flex gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Semua Perubahan</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function heroCarouselPicker(initial) {
    return {
        images: Array.isArray(initial) ? initial : [],
        open: false,
        loading: false,
        mediaItems: [],
        query: '',

        init() {
            this.$watch('open', val => { if (val) this.load(); });
            this.$watch('query', () => this.load());
        },

        async load() {
            this.loading = true;
            try {
                const res = await fetch(`/admin/media/json?q=${encodeURIComponent(this.query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                this.mediaItems = await res.json();
            } finally {
                this.loading = false;
            }
        },

        pick(item) {
            if (!this.images.includes(item.url)) {
                this.images.push(item.url);
            } else {
                this.images = this.images.filter(u => u !== item.url);
            }
        },

        remove(idx) {
            this.images.splice(idx, 1);
        },
    };
}
</script>
@endsection
