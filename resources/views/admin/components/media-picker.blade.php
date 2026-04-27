{{--
  Media Picker Component
  Usage:
    @include('admin.components.media-picker', [
        'inputName'    => 'hero_image',
        'inputId'      => 'hero_image',
        'currentValue' => $settings['hero_image'] ?? '',
        'label'        => 'Background Image',
    ])
--}}
@php
    $inputId    = $inputId    ?? Str::random(8);
    $label      = $label      ?? 'Image';
    $currentValue = $currentValue ?? '';
@endphp

<div x-data="mediaPicker_{{ $inputId }}()" class="space-y-2">
    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50">{{ $label }}</label>

    {{-- Hidden input that stores the URL --}}
    <input type="hidden" name="{{ $inputName }}" :value="selected" x-ref="hiddenInput">

    {{-- Preview + trigger button --}}
    <div class="flex items-start gap-4">
        <div class="w-24 h-24 bg-base-200 border border-base-300 flex items-center justify-center overflow-hidden shrink-0">
            <template x-if="selected">
                <img :src="selected" class="w-full h-full object-cover">
            </template>
            <template x-if="!selected">
                <span class="text-[9px] text-base-content/30 uppercase tracking-widest text-center px-1">No Image</span>
            </template>
        </div>
        <div class="space-y-2 pt-1">
            <button type="button" @click="open = true"
                    class="text-[10px] uppercase tracking-widest border border-base-content/30 px-3 py-1.5 hover:bg-base-content hover:text-base-100 transition-colors">
                Choose from Library
            </button>
            <template x-if="selected">
                <button type="button" @click="selected = ''"
                        class="block text-[10px] uppercase tracking-widest text-error hover:underline">
                    Remove
                </button>
            </template>
            <p class="text-[9px] text-base-content/40 max-w-[200px] truncate" x-text="selected || 'No image selected'"></p>
        </div>
    </div>

    {{-- Picker modal --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70">
        <div class="bg-base-100 w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-base-300 shrink-0">
                <h3 class="display-font text-2xl">Media Library</h3>
                <div class="flex items-center gap-3">
                    <input type="text" x-model.debounce.400ms="query" placeholder="Search..."
                           class="border-b border-base-content/20 bg-transparent py-1.5 text-xs w-40 focus:outline-none focus:border-primary">
                    <button type="button" @click.prevent.stop="open = false" class="text-base-content/40 hover:text-base-content text-xl leading-none">&times;</button>
                </div>
            </div>

            {{-- Grid --}}
            <div class="overflow-y-auto p-4 flex-1">
                <template x-if="loading">
                    <div class="py-12 text-center text-sm text-base-content/40">Loading…</div>
                </template>
                <template x-if="!loading && items.length === 0">
                    <div class="py-12 text-center text-sm text-base-content/40">No media found. <a href="{{ route('admin.media.index') }}" target="_blank" class="text-primary underline">Upload media ↗</a></div>
                </template>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                    <template x-for="item in items" :key="item.id">
                        <button type="button"
                                @click="pick(item)"
                                :class="selected === item.url ? 'ring-2 ring-primary ring-offset-1' : 'hover:opacity-80'"
                                class="aspect-square bg-base-200 overflow-hidden relative transition-all">
                            <template x-if="item.is_image">
                                <img :src="item.url" :alt="item.alt" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!item.is_image">
                                <span class="text-[9px] uppercase tracking-widest text-base-content/40" x-text="item.original_name"></span>
                            </template>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3 border-t border-base-300 flex items-center justify-between shrink-0">
                <a href="{{ route('admin.media.index') }}" target="_blank"
                   class="text-[10px] uppercase tracking-widest text-primary hover:underline">
                    Manage Library ↗
                </a>
                <button type="button" @click.prevent.stop="open = false"
                        class="text-[11px] uppercase tracking-widest text-base-content/50 hover:text-base-content">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function mediaPicker_{{ $inputId }}() {
    return {
        open: false,
        loading: false,
        items: [],
        selected: @js($currentValue),
        query: '',

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
                this.items = await res.json();
            } finally {
                this.loading = false;
            }
        },

        pick(item) {
            this.selected = item.url;
            this.open = false;
        },
    };
}
</script>
