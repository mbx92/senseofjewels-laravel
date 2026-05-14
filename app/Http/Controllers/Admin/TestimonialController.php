<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
use App\Models\Testimonial;
use App\Support\AdminMediaPath;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialController extends Controller
{
    public function index(): Response
    {
        $testimonials = Testimonial::query()->orderBy('sort_order')->orderBy('id')->get();

        return Inertia::render('Admin/Testimonials/Index', [
            'testimonials' => $testimonials->map(fn (Testimonial $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'rating' => $t->rating,
                'sort_order' => (int) $t->sort_order,
                'is_featured' => (bool) $t->is_featured,
                'is_active' => (bool) $t->is_active,
            ])->values()->all(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Testimonials/Create');
    }

    public function store(TestimonialRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('photo', 'photo_url');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('images/testimonials', 'public');
        } elseif ($request->filled('photo_url')) {
            $data['photo_path'] = AdminMediaPath::fromPublicUrl($request->string('photo_url')->toString());
        }

        Testimonial::query()->create($data);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial berhasil ditambahkan.');
    }

    public function edit(Testimonial $testimonial): Response
    {
        return Inertia::render('Admin/Testimonials/Edit', [
            'testimonial' => [
                'id' => $testimonial->id,
                'name' => $testimonial->name,
                'position' => $testimonial->position,
                'company' => $testimonial->company,
                'rating' => $testimonial->rating,
                'message' => $testimonial->message,
                'photo_url' => $testimonial->photo_path ? Storage::url($testimonial->photo_path) : '',
                'sort_order' => (int) $testimonial->sort_order,
                'is_featured' => (bool) $testimonial->is_featured,
                'is_active' => (bool) $testimonial->is_active,
            ],
        ]);
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $data = $request->safe()->except('photo', 'photo_url');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            if ($testimonial->photo_path) {
                Storage::disk('public')->delete($testimonial->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('images/testimonials', 'public');
        } elseif ($request->filled('photo_url')) {
            if ($testimonial->photo_path) {
                Storage::disk('public')->delete($testimonial->photo_path);
            }
            $data['photo_path'] = AdminMediaPath::fromPublicUrl($request->string('photo_url')->toString());
        }

        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial berhasil diperbarui.');
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        if ($testimonial->photo_path) {
            Storage::disk('public')->delete($testimonial->photo_path);
        }

        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial berhasil dihapus.');
    }
}
