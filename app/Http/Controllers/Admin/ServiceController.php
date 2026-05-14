<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\Support\AdminMediaPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = Service::query()->orderBy('sort_order')->orderBy('id')->get();

        return Inertia::render('Admin/Services/Index', [
            'services' => $services->map(fn (Service $s) => [
                'id' => $s->id,
                'title' => $s->title,
                'slug' => $s->slug,
                'sort_order' => (int) $s->sort_order,
                'is_featured' => (bool) $s->is_featured,
                'is_active' => (bool) $s->is_active,
            ])->values()->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Services/Create');
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image', 'image_url');
        $data['slug'] = Str::slug($request->title).'-'.Str::random(4);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('images/services', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image_path'] = AdminMediaPath::fromPublicUrl($request->string('image_url')->toString());
        }

        Service::query()->create($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service berhasil ditambahkan.');
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('Admin/Services/Edit', [
            'service' => [
                'id' => $service->id,
                'title' => $service->title,
                'icon' => $service->icon,
                'summary' => $service->summary,
                'description' => $service->description,
                'image_url' => $service->image_path ? Storage::url($service->image_path) : '',
                'sort_order' => (int) $service->sort_order,
                'is_featured' => (bool) $service->is_featured,
                'is_active' => (bool) $service->is_active,
            ],
        ]);
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $data = $request->safe()->except('image', 'image_url');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }
            $data['image_path'] = $request->file('image')->store('images/services', 'public');
        } elseif ($request->filled('image_url')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }
            $data['image_path'] = AdminMediaPath::fromPublicUrl($request->string('image_url')->toString());
        }

        $service->update($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service berhasil diperbarui.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service berhasil dihapus.');
    }
}
