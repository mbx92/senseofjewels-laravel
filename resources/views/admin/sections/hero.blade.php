@extends('layouts.admin')

@section('page-title', 'Hero Section')

@section('content')
@php
    $s  = $section?->settings ?? [];
    $sv = fn(string $key, string $default = '') => old($key, $s[$key] ?? $default);
    $existingHeroSlides = $s['hero_slides'] ?? [];
    if (! is_array($existingHeroSlides) || count($existingHeroSlides) === 0) {
        $legacyImages = $s['hero_images'] ?? [];
        if (empty($legacyImages) && $section?->image_url) {
            $legacyImages = [$section->image_url];
        }
        if (is_array($legacyImages) && count($legacyImages) > 0) {
            $existingHeroSlides = collect($legacyImages)->map(function (string $image, int $index) use ($section, $s) {
                return [
                    'image' => $image,
                    'title' => $index === 0 ? (string) ($section?->title ?? '') : '',
                    'subtitle' => $index === 0 ? (string) ($section?->subtitle ?? '') : '',
                    'description' => $index === 0 ? (string) ($section?->content ?? '') : '',
                    'cta_text' => $index === 0 ? (string) ($section?->cta_text ?? '') : '',
                    'cta_url' => $index === 0 ? (string) ($section?->cta_url ?? '') : '',
                    'text_position' => $index === 0 ? (string) ($s['text_position'] ?? 'top-left') : 'top-left',
                    'focus_x' => 50,
                    'focus_y' => 50,
                    'zoom' => 100,
                ];
            })->values()->all();
        }
    }
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

            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Slide Campaign Hero</span>
                    <span class="label-text-alt text-base-content/40">Setiap slide punya gambar, text, dan tombol sendiri</span>
                </label>

                <div x-data="heroSlidesEditor(@js($existingHeroSlides), @js($posBtns))" class="space-y-4">
                    <input type="hidden" name="hero_slides" x-ref="jsonInput" x-effect="$refs.jsonInput.value = JSON.stringify(slides)">

                    <div class="rounded-xl border border-base-300 bg-base-200/30 p-3">
                        <p class="mb-2 text-xs font-medium text-base-content/70">Preview semua slide (seperti landing)</p>
                        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            <template x-for="(slide, index) in slides" :key="'preview-' + index">
                                <div class="relative aspect-[4/3] overflow-hidden rounded-lg border border-base-300 bg-base-100">
                                    <template x-if="slide.image">
                                        <img :src="slide.image"
                                             class="h-full w-full"
                                             :style="`object-fit:cover;object-position:${slide.focus_x}% ${slide.focus_y}%;transform:scale(${slide.zoom / 100});transform-origin:center;`"
                                             alt="">
                                    </template>
                                    <template x-if="!slide.image">
                                        <div class="flex h-full w-full items-center justify-center text-[10px] uppercase tracking-widest text-base-content/40">No image</div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <template x-for="(slide, index) in slides" :key="index">
                        <div class="rounded-xl border border-base-300 bg-base-200/40 p-4 space-y-4">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-semibold text-sm">Slide <span x-text="index + 1"></span></h3>
                                <button type="button" class="btn btn-ghost btn-xs text-error" @click="removeSlide(index)" x-show="slides.length > 1">
                                    Hapus
                                </button>
                            </div>

                            <div class="grid gap-4 md:grid-cols-[160px,1fr]">
                                <div class="space-y-2">
                                    <div class="relative aspect-[4/3] w-full max-w-[220px] overflow-hidden rounded-lg border border-base-300 bg-base-100 cursor-grab active:cursor-grabbing"
                                         @mousedown.prevent="startDrag(index, $event)"
                                         @mousemove.prevent="onDrag($event)"
                                         @mouseup="stopDrag()"
                                         @mouseleave="stopDrag()"
                                         @touchstart.prevent="startDrag(index, $event)"
                                         @touchmove.prevent="onDrag($event)"
                                         @touchend="stopDrag()">
                                        <template x-if="slide.image">
                                            <img :src="slide.image"
                                                 class="h-full w-full"
                                                 :style="`object-fit:cover;object-position:${slide.focus_x}% ${slide.focus_y}%;transform:scale(${slide.zoom / 100});transform-origin:center;`"
                                                 alt="">
                                        </template>
                                        <div class="pointer-events-none absolute inset-0 border border-white/30"></div>
                                        <div class="pointer-events-none absolute top-1 right-1 rounded bg-black/45 px-1.5 py-0.5 text-[9px] text-white">
                                            Drag untuk geser
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline btn-xs w-full max-w-[220px]" @click="openImagePicker(index)">Pilih Gambar</button>
                                </div>

                                <div class="space-y-3">
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div class="form-control">
                                            <label class="label"><span class="label-text font-medium">Judul</span></label>
                                            <input type="text" x-model="slide.title" maxlength="40" class="input input-bordered w-full input-sm" placeholder="Timeless">
                                        </div>
                                        <div class="form-control">
                                            <label class="label"><span class="label-text font-medium">Tagline</span></label>
                                            <input type="text" x-model="slide.subtitle" maxlength="40" class="input input-bordered w-full input-sm" placeholder="Elegance">
                                        </div>
                                    </div>

                                    <div class="form-control">
                                        <label class="label"><span class="label-text font-medium">Deskripsi</span></label>
                                        <textarea x-model="slide.description" rows="2" class="textarea textarea-bordered w-full text-sm"
                                            placeholder="Handcrafted fine jewelry designed for the modern everyday."></textarea>
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div class="form-control">
                                            <label class="label"><span class="label-text font-medium">Teks Tombol CTA</span></label>
                                            <input type="text" x-model="slide.cta_text" class="input input-bordered w-full input-sm" placeholder="SHOP COLLECTION">
                                        </div>
                                        <div class="form-control">
                                            <label class="label"><span class="label-text font-medium">Link CTA</span></label>
                                            <input type="text" x-model="slide.cta_url" class="input input-bordered w-full input-sm" placeholder="/shop">
                                        </div>
                                    </div>

                                    <div class="form-control">
                                        <label class="label"><span class="label-text font-medium">Posisi Teks</span></label>
                                        <div style="display:inline-grid;grid-template-columns:repeat(3,34px);gap:3px;">
                                            <template x-for="[pos, direction] in positions" :key="pos">
                                                <button type="button"
                                                    @click="slide.text_position = pos"
                                                    :class="slide.text_position === pos ? 'btn btn-xs btn-primary' : 'btn btn-xs btn-ghost'"
                                                    style="width:34px;height:34px;min-height:0;padding:0;"
                                                    :title="pos">
                                                    <template x-if="direction === 'center'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <circle cx="10" cy="10" r="2.5" />
                                                        </svg>
                                                    </template>
                                                    <template x-if="direction !== 'center'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path x-show="direction === 'up-left'" fill-rule="evenodd" d="M14.78 14.78a.75.75 0 0 1-1.06 0L6 7v5.25a.75.75 0 0 1-1.5 0V5.19A1.19 1.19 0 0 1 5.69 4h7.06a.75.75 0 0 1 0 1.5H7l7.78 7.72a.75.75 0 0 1 0 1.06Z" clip-rule="evenodd" />
                                                            <path x-show="direction === 'up'" fill-rule="evenodd" d="M10 16a.75.75 0 0 1-.75-.75V6.81L6.28 9.78a.75.75 0 1 1-1.06-1.06l4.25-4.25a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 1 1-1.06 1.06L10.75 6.8v8.44A.75.75 0 0 1 10 16Z" clip-rule="evenodd" />
                                                            <path x-show="direction === 'up-right'" fill-rule="evenodd" d="M5.22 14.78a.75.75 0 0 1 0-1.06L13 6h-5.25a.75.75 0 0 1 0-1.5h7.06A1.19 1.19 0 0 1 16 5.69v7.06a.75.75 0 0 1-1.5 0V7l-7.72 7.78a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                                                            <path x-show="direction === 'left'" fill-rule="evenodd" d="M16 10a.75.75 0 0 1-.75.75H6.81l2.97 2.97a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 1.06L6.8 9.25h8.44A.75.75 0 0 1 16 10Z" clip-rule="evenodd" />
                                                            <path x-show="direction === 'right'" fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h8.44L10.22 6.28a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06l2.97-2.97H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" />
                                                            <path x-show="direction === 'down-left'" fill-rule="evenodd" d="M14.78 5.22a.75.75 0 0 1 0 1.06L7 14h5.25a.75.75 0 0 1 0 1.5H5.19A1.19 1.19 0 0 1 4 14.31V7.25a.75.75 0 0 1 1.5 0V13l7.72-7.78a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                                                            <path x-show="direction === 'down'" fill-rule="evenodd" d="M10 4a.75.75 0 0 1 .75.75v8.44l2.97-2.97a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 1 1 1.06-1.06l2.97 2.97V4.75A.75.75 0 0 1 10 4Z" clip-rule="evenodd" />
                                                            <path x-show="direction === 'down-right'" fill-rule="evenodd" d="M5.22 5.22a.75.75 0 0 1 1.06 0L14 13V7.75a.75.75 0 0 1 1.5 0v7.06A1.19 1.19 0 0 1 14.31 16H7.25a.75.75 0 0 1 0-1.5H13L5.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                        </svg>
                                                    </template>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="rounded-lg border border-base-300 bg-base-100/70 p-3 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <p class="text-xs font-medium">Posisi & Crop Gambar</p>
                                            <p class="text-[11px] text-base-content/60">Geser fokus dengan panah + atur zoom</p>
                                        </div>

                                        <div class="grid gap-3 md:grid-cols-2">
                                            <div class="space-y-1">
                                                <label class="text-xs text-base-content/70">Horizontal (<span x-text="slide.focus_x"></span>%)</label>
                                                <input type="range" min="0" max="100" x-model.number="slide.focus_x" class="range range-xs">
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs text-base-content/70">Vertical (<span x-text="slide.focus_y"></span>%)</label>
                                                <input type="range" min="0" max="100" x-model.number="slide.focus_y" class="range range-xs">
                                            </div>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-xs text-base-content/70">Zoom Crop (<span x-text="slide.zoom"></span>%)</label>
                                            <input type="range" min="80" max="160" x-model.number="slide.zoom" class="range range-xs">
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button type="button" class="btn btn-ghost btn-xs" @click="slide.focus_x = Math.max(0, slide.focus_x - 5)">←</button>
                                            <button type="button" class="btn btn-ghost btn-xs" @click="slide.focus_y = Math.max(0, slide.focus_y - 5)">↑</button>
                                            <button type="button" class="btn btn-ghost btn-xs" @click="slide.focus_y = Math.min(100, slide.focus_y + 5)">↓</button>
                                            <button type="button" class="btn btn-ghost btn-xs" @click="slide.focus_x = Math.min(100, slide.focus_x + 5)">→</button>
                                            <button type="button" class="btn btn-outline btn-xs ml-auto" @click="slide.focus_x = 50; slide.focus_y = 50; slide.zoom = 100;">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-between items-center pt-2">
                        <button type="button" class="btn btn-outline btn-sm" @click="addSlide()">+ Tambah Slide</button>
                        <p class="text-xs text-base-content/50">Urutan slide mengikuti urutan list ini.</p>
                    </div>

                    @error('hero_slides')
                        <p class="text-xs text-error">{{ $message }}</p>
                    @enderror

                    <div x-show="open" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70">
                        <div class="bg-base-100 w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl">
                            <div class="flex items-center justify-between px-6 py-4 border-b border-base-300 shrink-0">
                                <h3 class="display-font text-2xl">Media Library</h3>
                                <div class="flex items-center gap-3">
                                    <input type="text" x-model.debounce.400ms="query" placeholder="Search..."
                                           class="border-b border-base-content/20 bg-transparent py-1.5 text-xs w-40 focus:outline-none focus:border-primary">
                                    <button @click="open = false" type="button" class="text-base-content/40 hover:text-base-content text-xl leading-none">&times;</button>
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
                                        <button type="button" @click="pickImage(item.url)"
                                                class="aspect-square bg-base-200 overflow-hidden relative transition-all hover:opacity-80">
                                            <template x-if="item.is_image">
                                                <img :src="item.url" :alt="item.alt" class="w-full h-full object-cover">
                                            </template>
                                        </button>
                                    </template>
                                </div>
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
    <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-base-300 bg-base-100/95 px-5 shadow-[0_-4px_24px_rgba(0,0,0,0.08)] backdrop-blur lg:left-72 lg:px-8"
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
function heroSlidesEditor(initial, positions) {
    return {
        slides: Array.isArray(initial) && initial.length
            ? initial.map((slide) => ({
                image: slide.image || '',
                title: slide.title || '',
                subtitle: slide.subtitle || '',
                description: slide.description || '',
                cta_text: slide.cta_text || '',
                cta_url: slide.cta_url || '',
                text_position: slide.text_position || 'top-left',
                focus_x: Number.isFinite(Number(slide.focus_x)) ? Number(slide.focus_x) : 50,
                focus_y: Number.isFinite(Number(slide.focus_y)) ? Number(slide.focus_y) : 50,
                zoom: Number.isFinite(Number(slide.zoom)) ? Number(slide.zoom) : 100,
            }))
            : [{
                image: '',
                title: '',
                subtitle: '',
                description: '',
                cta_text: '',
                cta_url: '',
                text_position: 'top-left',
                focus_x: 50,
                focus_y: 50,
                zoom: 100,
            }],
        positions: Array.isArray(positions) ? positions : [],
        open: false,
        selectedSlideIndex: null,
        loading: false,
        mediaItems: [],
        query: '',
        dragging: null,

        init() {
            this.$watch('open', val => { if (val) this.load(); });
            this.$watch('query', () => this.load());
        },

        async load() {
            this.loading = true;
            try {
                const endpoint = `{{ route('admin.media.json', [], false) }}?q=${encodeURIComponent(this.query)}`;
                const res = await fetch(endpoint, {
                    credentials: 'same-origin',
                    mode: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                this.mediaItems = await res.json();
            } finally {
                this.loading = false;
            }
        },

        addSlide() {
            this.slides.push({
                image: '',
                title: '',
                subtitle: '',
                description: '',
                cta_text: '',
                cta_url: '',
                text_position: 'top-left',
                focus_x: 50,
                focus_y: 50,
                zoom: 100,
            });
        },

        removeSlide(index) {
            if (this.slides.length <= 1) return;
            this.slides.splice(index, 1);
        },

        openImagePicker(index) {
            this.selectedSlideIndex = index;
            this.open = true;
        },

        pickImage(url) {
            if (this.selectedSlideIndex === null) return;
            if (!this.slides[this.selectedSlideIndex]) return;
            this.slides[this.selectedSlideIndex].image = url;
            this.open = false;
        },

        getPoint(event) {
            if (event.touches && event.touches[0]) {
                return { x: event.touches[0].clientX, y: event.touches[0].clientY };
            }
            return { x: event.clientX, y: event.clientY };
        },

        startDrag(index, event) {
            if (!this.slides[index] || !this.slides[index].image) return;
            const point = this.getPoint(event);
            this.dragging = {
                index,
                startX: point.x,
                startY: point.y,
                startFocusX: this.slides[index].focus_x,
                startFocusY: this.slides[index].focus_y,
            };
        },

        onDrag(event) {
            if (!this.dragging) return;
            const point = this.getPoint(event);
            const dx = point.x - this.dragging.startX;
            const dy = point.y - this.dragging.startY;
            const active = this.slides[this.dragging.index];
            if (!active) return;

            active.focus_x = Math.max(0, Math.min(100, this.dragging.startFocusX - dx * 0.18));
            active.focus_y = Math.max(0, Math.min(100, this.dragging.startFocusY - dy * 0.25));
        },

        stopDrag() {
            this.dragging = null;
        },
    };
}
</script>
@endsection
