<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaController extends Controller
{
    private const ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml',
    ];

    private const MAX_SIZE_BYTES = 4 * 1024 * 1024; // 4 MB

    public function index(Request $request): View
    {
        $media = Media::query()
            ->when($request->filled('collection'), fn ($q) => $q->where('collection', $request->collection))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('original_name', 'like', "%{$request->search}%")
                      ->orWhere('alt', 'like', "%{$request->search}%");
            }))
            ->latest()
            ->paginate(36)
            ->withQueryString();

        $collections = Media::query()->select('collection')->distinct()->pluck('collection');

        return view('admin.media.index', compact('media', 'collections'));
    }

    /**
     * Return JSON list for the media picker modal (AJAX).
     */
    public function json(Request $request): JsonResponse
    {
        $items = Media::query()
            ->when($request->filled('q'), fn ($q) => $q->where('original_name', 'like', "%{$request->q}%"))
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (Media $m) => [
                'id'            => $m->id,
                'url'           => $m->url,
                'original_name' => $m->original_name,
                'alt'           => $m->alt,
                'human_size'    => $m->human_size,
                'is_image'      => $m->isImage(),
            ]);

        return response()->json($items);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'files'              => ['required', 'array', 'max:20'],
            'files.*'            => ['required', 'file', 'max:4096'],
            'collection'         => ['nullable', 'string', 'max:50'],
        ]);

        $collection = $request->input('collection', 'general');

        foreach ($request->file('files') as $file) {
            // Validate mime manually (stricter than just mimes: rule)
            if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
                continue;
            }
            if ($file->getSize() > self::MAX_SIZE_BYTES) {
                continue;
            }

            $slug      = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $ext       = strtolower($file->getClientOriginalExtension());
            $filename  = $slug . '-' . Str::random(8) . '.' . $ext;
            $path      = $file->storeAs("media/{$collection}", $filename, 'public');

            Media::create([
                'disk'          => 'public',
                'path'          => $path,
                'filename'      => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
                'collection'    => $collection,
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'Media berhasil diupload.');
    }

    public function update(Request $request, Media $medium): RedirectResponse
    {
        $validated = $request->validate([
            'alt'   => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $medium->update($validated);

        return redirect()->route('admin.media.index')
            ->with('success', 'Meta media berhasil diperbarui.');
    }

    public function destroy(Media $medium): RedirectResponse
    {
        Storage::disk($medium->disk)->delete($medium->path);
        $medium->delete();

        return redirect()->route('admin.media.index')
            ->with('success', 'Media berhasil dihapus.');
    }
}
