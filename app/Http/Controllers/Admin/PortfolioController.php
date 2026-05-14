<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PortfolioItemRequest;
use App\Models\PortfolioItem;
use App\Support\AdminMediaPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function index(): Response
    {
        $items = PortfolioItem::query()->orderBy('sort_order')->orderBy('id')->get();

        return Inertia::render('Admin/Portfolio/Index', [
            'items' => $items->map(fn (PortfolioItem $p) => [
                'id' => $p->id,
                'title' => $p->title,
                'category' => $p->category,
                'sort_order' => (int) $p->sort_order,
                'is_featured' => (bool) $p->is_featured,
                'is_active' => (bool) $p->is_active,
            ])->values()->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Portfolio/Create');
    }

    public function store(PortfolioItemRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image', 'image_url');
        $data['slug'] = Str::slug($request->title).'-'.Str::random(4);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('images/portfolio', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image_path'] = AdminMediaPath::fromPublicUrl($request->string('image_url')->toString());
        }

        PortfolioItem::query()->create($data);

        return redirect()->route('admin.portfolio.index')
            ->with('success', 'Portfolio item berhasil ditambahkan.');
    }

    public function edit(PortfolioItem $portfolio): Response
    {
        return Inertia::render('Admin/Portfolio/Edit', [
            'portfolio' => [
                'id' => $portfolio->id,
                'title' => $portfolio->title,
                'category' => $portfolio->category,
                'client_name' => $portfolio->client_name,
                'project_url' => $portfolio->project_url,
                'description' => $portfolio->description,
                'image_url' => $portfolio->image_path ? Storage::url($portfolio->image_path) : '',
                'completed_at' => $portfolio->completed_at?->format('Y-m-d'),
                'sort_order' => (int) $portfolio->sort_order,
                'is_featured' => (bool) $portfolio->is_featured,
                'is_active' => (bool) $portfolio->is_active,
            ],
        ]);
    }

    public function update(PortfolioItemRequest $request, PortfolioItem $portfolio): RedirectResponse
    {
        $data = $request->safe()->except('image', 'image_url');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($portfolio->image_path) {
                Storage::disk('public')->delete($portfolio->image_path);
            }
            $data['image_path'] = $request->file('image')->store('images/portfolio', 'public');
        } elseif ($request->filled('image_url')) {
            if ($portfolio->image_path) {
                Storage::disk('public')->delete($portfolio->image_path);
            }
            $data['image_path'] = AdminMediaPath::fromPublicUrl($request->string('image_url')->toString());
        }

        $portfolio->update($data);

        return redirect()->route('admin.portfolio.index')
            ->with('success', 'Portfolio item berhasil diperbarui.');
    }

    public function destroy(PortfolioItem $portfolio): RedirectResponse
    {
        if ($portfolio->image_path) {
            Storage::disk('public')->delete($portfolio->image_path);
        }

        $portfolio->delete();

        return redirect()->route('admin.portfolio.index')
            ->with('success', 'Portfolio item berhasil dihapus.');
    }
}
