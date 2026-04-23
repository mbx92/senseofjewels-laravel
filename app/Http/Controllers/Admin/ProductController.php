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
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['category', 'images' => fn ($q) => $q->where('is_primary', true)])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data               = $request->safe()->except(['images', 'media_image_urls']);
        $data['slug']       = Str::slug($request->name) . '-' . Str::random(4);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']  = $request->boolean('is_active', true);
        $data['published_at'] = $data['is_active'] ? now() : null;

        $product = Product::query()->create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('images/products', 'public');
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'alt_text'   => $product->name,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        if ($request->filled('media_image_urls')) {
            $fileCount = $request->hasFile('images') ? count($request->file('images')) : 0;
            foreach (array_filter((array) $request->input('media_image_urls')) as $index => $url) {
                $path = ltrim(str_replace(Storage::disk('public')->url(''), '', $url), '/');
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'alt_text'   => $product->name,
                    'is_primary' => ($fileCount === 0 && $index === 0),
                    'sort_order' => $fileCount + $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();
        $product->load('images');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data               = $request->safe()->except(['images', 'media_image_urls']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']  = $request->boolean('is_active');

        $product->update($data);

        if ($request->hasFile('images')) {
            $existingCount = $product->images()->count();
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('images/products', 'public');
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'alt_text'   => $product->name,
                    'is_primary' => $existingCount === 0 && $index === 0,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }

        if ($request->filled('media_image_urls')) {
            $existingCount = $product->images()->count();
            $fileCount = $request->hasFile('images') ? count($request->file('images')) : 0;
            foreach (array_filter((array) $request->input('media_image_urls')) as $index => $url) {
                $path = ltrim(str_replace(Storage::disk('public')->url(''), '', $url), '/');
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'path'       => $path,
                    'alt_text'   => $product->name,
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
}
