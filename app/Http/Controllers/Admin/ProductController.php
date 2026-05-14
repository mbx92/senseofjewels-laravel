<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        $products = Product::query()
            ->with(['category', 'images' => fn ($q) => $q->where('is_primary', true)])
            ->orderByDesc('created_at')
            ->paginate(20)
            ->through(function (Product $product) {
                $primary = $product->images->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => (float) $product->price,
                    'price_formatted' => 'Rp '.number_format((int) $product->price, 0, ',', '.'),
                    'stock' => (int) $product->stock,
                    'min_stock_alert' => (int) $product->min_stock_alert,
                    'is_featured' => (bool) $product->is_featured,
                    'is_active' => (bool) $product->is_active,
                    'category_name' => $product->category?->name,
                    'primary_image_url' => $primary ? Storage::url($primary->path) : null,
                ];
            });

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
        ]);
    }

    public function create(): Response
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['images', 'media_image_urls', 'media_image_urls_json', 'specifications_text']);
        $data['slug'] = Str::slug($request->name).'-'.Str::random(4);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['published_at'] = $data['is_active'] ? now() : null;
        $data['specifications'] = $this->parseSpecifications($request->input('specifications_text'));

        $product = Product::query()->create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('images/products', 'public');
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        $mediaUrls = $this->extractMediaImageUrls($request);
        if ($mediaUrls !== []) {
            $fileCount = $request->hasFile('images') ? count($request->file('images')) : 0;
            foreach ($mediaUrls as $index => $url) {
                $path = $this->mediaUrlToPath($url);
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => ($fileCount === 0 && $index === 0),
                    'sort_order' => $fileCount + $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): Response
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $product->load(['images' => fn ($q) => $q->orderBy('sort_order')]);

        return Inertia::render('Admin/Products/Edit', [
            'categories' => $categories,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'category_id' => $product->category_id,
                'short_description' => $product->short_description,
                'description' => $product->description,
                'specifications_text' => collect($product->specifications ?? [])
                    ->map(fn ($value, $label) => "{$label}: {$value}")
                    ->implode("\n"),
                'price' => $product->price,
                'cost_price' => $product->cost_price,
                'stock' => $product->stock,
                'min_stock_alert' => $product->min_stock_alert,
                'weight' => $product->weight,
                'is_active' => $product->is_active,
                'is_featured' => $product->is_featured,
                'images' => $product->images->map(fn ($img) => [
                    'id' => $img->id,
                    'product_id' => $product->id,
                    'url' => Storage::url($img->path),
                    'is_primary' => $img->is_primary,
                    'alt_text' => $img->alt_text,
                ])->values()->all(),
            ],
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->except(['images', 'media_image_urls', 'media_image_urls_json', 'specifications_text']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['specifications'] = $this->parseSpecifications($request->input('specifications_text'));

        $product->update($data);

        if ($request->hasFile('images')) {
            $existingCount = $product->images()->count();
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('images/products', 'public');
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => $existingCount === 0 && $index === 0,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }

        $mediaUrls = $this->extractMediaImageUrls($request);
        if ($mediaUrls !== []) {
            $existingCount = $product->images()->count();
            $fileCount = $request->hasFile('images') ? count($request->file('images')) : 0;
            foreach ($mediaUrls as $index => $url) {
                $path = $this->mediaUrlToPath($url);
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'alt_text' => $product->name,
                    'is_primary' => ($existingCount === 0 && $fileCount === 0 && $index === 0),
                    'sort_order' => $existingCount + $fileCount + $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $product->images()->delete();
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function destroyImage(ProductImage $image): RedirectResponse
    {
        $productId = $image->product_id;
        Storage::disk('public')->delete($image->path);
        $image->delete();

        // If deleted image was primary, promote next one
        $product = Product::find($productId);
        if ($product && ! $product->images()->where('is_primary', true)->exists()) {
            $product->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Gambar dihapus.');
    }

    private function parseSpecifications(?string $specificationsText): ?array
    {
        if (! $specificationsText) {
            return null;
        }

        $specifications = [];
        foreach (preg_split('/\r\n|\r|\n/', trim($specificationsText)) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            [$label, $value] = array_pad(explode(':', $line, 2), 2, '');
            $label = trim($label);
            $value = trim($value);

            if ($label === '' || $value === '') {
                continue;
            }

            $specifications[$label] = $value;
        }

        return $specifications !== [] ? $specifications : null;
    }

    private function extractMediaImageUrls(ProductRequest $request): array
    {
        if ($request->filled('media_image_urls_json')) {
            $decoded = json_decode((string) $request->input('media_image_urls_json'), true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('trim', $decoded)));
            }
        }

        if ($request->filled('media_image_urls')) {
            return array_values(array_filter(array_map('trim', (array) $request->input('media_image_urls'))));
        }

        return [];
    }

    private function mediaUrlToPath(string $url): string
    {
        $url = trim($url);
        $storagePrefix = '/storage/';

        if (Str::startsWith($url, ['http://', 'https://'])) {
            $url = parse_url($url, PHP_URL_PATH) ?: $url;
        }

        if (Str::startsWith($url, $storagePrefix)) {
            return ltrim(Str::after($url, $storagePrefix), '/');
        }

        $publicBaseUrl = Storage::disk('public')->url('');
        $normalized = str_replace('\\', '/', $url);
        $normalizedBase = str_replace('\\', '/', $publicBaseUrl);

        if ($normalizedBase !== '' && Str::startsWith($normalized, $normalizedBase)) {
            return ltrim(Str::after($normalized, $normalizedBase), '/');
        }

        return ltrim($normalized, '/');
    }
}
