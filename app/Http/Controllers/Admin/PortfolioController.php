<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PortfolioItemRequest;
use App\Models\PortfolioItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $items = PortfolioItem::query()->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.portfolio.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.portfolio.create');
    }

    public function store(PortfolioItemRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['slug']        = Str::slug($request->title) . '-' . Str::random(4);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('images/portfolio', 'public');
        }

        PortfolioItem::query()->create($data);

        return redirect()->route('admin.portfolio.index')
            ->with('success', 'Portfolio item berhasil ditambahkan.');
    }

    public function edit(PortfolioItem $portfolio): View
    {
        return view('admin.portfolio.edit', compact('portfolio'));
    }

    public function update(PortfolioItemRequest $request, PortfolioItem $portfolio): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active']   = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($portfolio->image_path) {
                Storage::disk('public')->delete($portfolio->image_path);
            }
            $data['image_path'] = $request->file('image')->store('images/portfolio', 'public');
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
