@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="mediaLibrary()">

    {{-- Heading + Upload --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div class="space-y-1">
            <p class="text-[10px] uppercase tracking-[0.25em] text-primary">System</p>
            <h1 class="display-font text-4xl text-base-content font-normal">Media Library</h1>
            <p class="text-sm text-base-content/50">{{ $media->total() }} file tersimpan</p>
        </div>
        <button @click="uploadOpen = true"
                class="text-[11px] uppercase tracking-widest border border-base-content/30 px-4 py-2 hover:bg-base-content hover:text-base-100 transition-colors shrink-0">
            + Upload Media
        </button>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.media.index') }}" class="flex items-center gap-4 flex-wrap"
          x-data="{ timer: null, autoSubmit(delay = 350) { clearTimeout(this.timer); this.timer = setTimeout(() => this.$refs.form.submit(), delay); } }"
          x-ref="form">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search filename, alt text..."
               @input="autoSubmit()"
               class="border-b border-base-content/20 bg-transparent py-1.5 text-xs placeholder:text-base-content/40 focus:outline-none focus:border-primary transition-colors w-52">
        <select name="collection" @change="autoSubmit(0)" class="border-b border-base-content/20 bg-transparent py-1.5 text-xs focus:outline-none focus:border-primary transition-colors">
            <option value="">All Collections</option>
            @foreach($collections as $col)
                <option value="{{ $col }}" {{ request('collection') === $col ? 'selected' : '' }}>{{ ucfirst($col) }}</option>
            @endforeach
        </select>
        @if(request()->hasAny(['search', 'collection']))
        <a href="{{ route('admin.media.index') }}" class="text-[10px] uppercase tracking-widest text-base-content/40 hover:text-base-content">Reset</a>
        @endif
    </form>

    {{-- Media Grid --}}
    @if($media->isEmpty())
    <div class="py-24 text-center border border-dashed border-base-300">
        <p class="text-base-content/40 text-sm">Belum ada media. Upload file pertama Anda.</p>
    </div>
    @else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach($media as $item)
        <div x-data="{ infoOpen: false }" class="group relative bg-base-200 border border-base-300 overflow-hidden">
            {{-- Thumbnail --}}
            <div class="aspect-square flex items-center justify-center overflow-hidden bg-base-200">
                @if($item->isImage())
                    <img src="{{ $item->url }}" alt="{{ $item->alt }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="text-base-content/30 text-xs tracking-widest uppercase text-center px-2">
                        {{ pathinfo($item->filename, PATHINFO_EXTENSION) }}
                    </div>
                @endif
            </div>
            {{-- Hover overlay --}}
            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 p-2">
                <button @click="infoOpen = true"
                        class="text-[10px] uppercase tracking-widest bg-base-100 text-base-content px-3 py-1.5 w-full hover:bg-primary hover:text-primary-content transition-colors">
                    Edit Alt
                </button>
                <button @click="copyToClipboard('{{ $item->url }}')"
                        class="text-[10px] uppercase tracking-widest border border-white/50 text-white px-3 py-1.5 w-full hover:bg-white/10 transition-colors">
                    Copy URL
                </button>
            </div>
            {{-- File name --}}
            <div class="px-2 py-1.5 bg-base-100 border-t border-base-300">
                <p class="text-[9px] text-base-content/50 truncate" title="{{ $item->original_name }}">{{ $item->original_name }}</p>
                <p class="text-[9px] text-base-content/30">{{ $item->human_size }}</p>
            </div>

            {{-- Edit alt / delete modal --}}
            <div x-show="infoOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
                <div class="bg-base-100 w-full max-w-sm p-6 shadow-2xl space-y-4">
                    <h3 class="display-font text-2xl">Edit Media</h3>
                    @if($item->isImage())
                    <img src="{{ $item->url }}" class="w-full aspect-video object-cover bg-base-200">
                    @endif
                    <p class="text-xs text-base-content/50 font-mono truncate">{{ $item->path }}</p>
                    <form method="POST" action="{{ route('admin.media.update', $item) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-1">Alt Text</label>
                            <input type="text" name="alt" value="{{ $item->alt }}"
                                   class="w-full border-b border-base-content/20 bg-transparent py-2 text-sm focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-1">Title</label>
                            <input type="text" name="title" value="{{ $item->title }}"
                                   class="w-full border-b border-base-content/20 bg-transparent py-2 text-sm focus:outline-none focus:border-primary">
                        </div>
                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="text-[10px] uppercase tracking-widest bg-neutral text-neutral-content px-4 py-2 hover:bg-neutral/80">Save</button>
                            <button type="button" @click="infoOpen = false" class="text-[10px] uppercase tracking-widest text-base-content/50">Cancel</button>
                        </div>
                    </form>
                    <div class="border-t border-base-300 pt-3">
                        <form method="POST" action="{{ route('admin.media.destroy', $item) }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Hapus file ini permanen?')"
                                    class="text-[10px] uppercase tracking-widest text-error hover:underline">
                                Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($media->hasPages())
    <div>{{ $media->links() }}</div>
    @endif
    @endif

    {{-- Upload Modal --}}
    <div x-show="uploadOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        <div class="bg-base-100 w-full max-w-lg p-8 shadow-2xl space-y-6">
            <h3 class="display-font text-2xl">Upload Media</h3>
            <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="space-y-5"
                  @submit="uploading = true">
                @csrf
                {{-- Drop zone --}}
                <div class="border-2 border-dashed border-base-300 p-8 text-center cursor-pointer hover:border-primary transition-colors"
                     @dragover.prevent @drop.prevent="handleDrop($event, $el.querySelector('input[type=file]'))">
                    <input type="file" name="files[]" multiple accept="image/*"
                           class="hidden" x-ref="fileInput"
                           @change="previewFiles($event)">
                    <button type="button" @click="$refs.fileInput.click()"
                            class="block w-full text-sm text-base-content/50">
                        <span class="block text-3xl mb-2 opacity-30">↑</span>
                        <span class="text-[11px] uppercase tracking-widest">Click to browse</span>
                        <span class="block text-[10px] text-base-content/30 mt-1">or drag & drop · JPG, PNG, WebP, GIF, SVG · max 4 MB each</span>
                    </button>
                    <template x-if="previews.length">
                        <div class="grid grid-cols-4 gap-2 mt-4">
                            <template x-for="src in previews" :key="src">
                                <img :src="src" class="aspect-square object-cover bg-base-200">
                            </template>
                        </div>
                    </template>
                </div>

                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Collection</label>
                    <select name="collection"
                            class="w-full border-b border-base-content/20 bg-transparent py-2 text-sm focus:outline-none focus:border-primary">
                        <option value="general">General</option>
                        <option value="products">Products</option>
                        <option value="sections">Landing Page Sections</option>
                        <option value="seo">SEO / OG Images</option>
                    </select>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" :disabled="uploading"
                            class="text-[11px] uppercase tracking-widest bg-neutral text-neutral-content px-6 py-2.5 hover:bg-neutral/80 transition-colors disabled:opacity-50">
                        <span x-show="!uploading">Upload</span>
                        <span x-show="uploading">Uploading…</span>
                    </button>
                    <button type="button" @click="uploadOpen = false; previews = []"
                            class="text-[11px] uppercase tracking-widest text-base-content/50 hover:text-base-content">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function mediaLibrary() {
    return {
        uploadOpen: false,
        uploading: false,
        previews: [],

        previewFiles(event) {
            this.previews = [];
            Array.from(event.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => this.previews.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },

        handleDrop(event, input) {
            const dt = new DataTransfer();
            Array.from(event.dataTransfer.files).forEach(f => dt.items.add(f));
            input.files = dt.files;
            this.previewFiles({ target: input });
        },

        copyToClipboard(url) {
            navigator.clipboard.writeText(url).then(() => {
                alert('URL disalin ke clipboard');
            });
        },
    };
}
</script>
@endpush
@endsection
