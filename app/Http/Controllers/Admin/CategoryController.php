<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        $categories = Category::query()
            ->with('parent:id,name')
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'parent_name' => $c->parent?->name,
                'products_count' => $c->products_count,
                'sort_order' => (int) $c->sort_order,
                'is_active' => (bool) $c->is_active,
            ]);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories->values()->all(),
        ]);
    }

    public function create(): Response
    {
        $parents = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Categories/Create', [
            'parents' => $parents,
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('images/categories', 'public');
        } elseif ($request->filled('image_url')) {
            $url = $request->input('image_url');
            $data['image_path'] = ltrim(str_replace(Storage::disk('public')->url(''), '', $url), '/');
        }

        Category::query()->create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): Response
    {
        $parents = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Categories/Edit', [
            'parents' => $parents,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'parent_id' => $category->parent_id,
                'description' => $category->description,
                'image_url' => $category->image_path ? Storage::url($category->image_path) : '',
                'sort_order' => (int) $category->sort_order,
                'is_active' => (bool) $category->is_active,
            ],
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image')->store('images/categories', 'public');
        } elseif ($request->filled('image_url')) {
            $url = $request->input('image_url');
            $data['image_path'] = ltrim(str_replace(Storage::disk('public')->url(''), '', $url), '/');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
        }

        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
