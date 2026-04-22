@extends('layouts.app')

@section('content')
    @php
        $hero = $sections->get('hero');
        $about = $sections->get('about');
        $contact = $sections->get('contact');
    @endphp

    <div class="space-y-10">
        <section class="hero overflow-hidden rounded-box bg-base-100 shadow-xl">
            <div class="hero-content grid gap-10 px-6 py-14 lg:grid-cols-[1.2fr,0.8fr] lg:px-12">
                <div>
                    <div class="badge badge-primary badge-outline">Laravel 11 + DaisyUI CMS</div>
                    <h1 class="mt-4 text-4xl font-black leading-tight lg:text-5xl">
                        {{ $hero?->title ?? $page?->title ?? 'Elegant company profile and commerce experience in one platform.' }}
                    </h1>
                    <p class="mt-4 max-w-2xl text-base-content/70">
                        {{ $hero?->subtitle ?? $page?->excerpt ?? 'This landing page is already wired to CMS-friendly models for hero, about, services, portfolio, testimonials, and contact details.' }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('shop.index') }}" class="btn btn-primary">
                            {{ $hero?->cta_text ?? 'Explore Products' }}
                        </a>
                        <a href="#contact" class="btn btn-outline">Contact Us</a>
                    </div>
                </div>
                <div class="card border border-base-300 bg-base-200 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title">Project Overview</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-box bg-base-100 p-4">
                                <div class="text-sm text-base-content/60">Services</div>
                                <div class="text-3xl font-semibold">{{ $services->count() }}</div>
                            </div>
                            <div class="rounded-box bg-base-100 p-4">
                                <div class="text-sm text-base-content/60">Portfolio Items</div>
                                <div class="text-3xl font-semibold">{{ $portfolioItems->count() }}</div>
                            </div>
                            <div class="rounded-box bg-base-100 p-4">
                                <div class="text-sm text-base-content/60">Testimonials</div>
                                <div class="text-3xl font-semibold">{{ $testimonials->count() }}</div>
                            </div>
                            <div class="rounded-box bg-base-100 p-4">
                                <div class="text-sm text-base-content/60">Theme</div>
                                <div class="text-3xl font-semibold">Corporate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="grid gap-6 lg:grid-cols-[0.9fr,1.1fr]">
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-3xl">{{ $about?->title ?? 'About the Brand' }}</h2>
                    <p class="text-base-content/70">
                        {!! nl2br(e($about?->content ?? 'Use the pages and sections tables from the CMS module to fully manage your company story, mission, and supporting visuals.')) !!}
                    </p>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="card bg-primary text-primary-content shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">CMS Managed</h3>
                        <p>Hero, about, services, portfolio, testimonials, and contact content are designed to be editable from admin screens.</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">Commerce Ready</h3>
                        <p>Catalog, cart, checkout, and order tracking scaffolding are already connected to the data layer.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-semibold">Services</h2>
                    <p class="text-base-content/70">DaisyUI cards are ready for your company profile service blocks.</p>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($services as $service)
                    <div class="card border border-base-300 bg-base-100 shadow-sm">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <h3 class="card-title">{{ $service->title }}</h3>
                                @if ($service->is_featured)
                                    <span class="badge badge-primary">Featured</span>
                                @endif
                            </div>
                            <p class="text-sm text-base-content/70">{{ $service->summary ?? $service->description }}</p>
                            @if (! empty($service->features))
                                <div class="flex flex-wrap gap-2 pt-2">
                                    @foreach ($service->features as $feature)
                                        <span class="badge badge-outline">{{ $feature }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="card border border-dashed border-base-300 bg-base-100 md:col-span-2 xl:col-span-3">
                        <div class="card-body">
                            <h3 class="card-title">No services yet</h3>
                            <p class="text-base-content/70">Populate the <code>services</code> table from the CMS admin to make this section live.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <section id="portfolio" class="space-y-4">
            <div>
                <h2 class="text-3xl font-semibold">Portfolio</h2>
                <p class="text-base-content/70">Use category tags, screenshots, and project descriptions to present your best work.</p>
            </div>
            <div class="carousel carousel-center w-full space-x-4 rounded-box">
                @forelse ($portfolioItems as $item)
                    <div class="carousel-item w-full max-w-sm">
                        <div class="card h-full w-full border border-base-300 bg-base-100 shadow-sm">
                            <div class="card-body">
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="card-title">{{ $item->title }}</h3>
                                    @if ($item->category)
                                        <span class="badge badge-outline">{{ $item->category }}</span>
                                    @endif
                                </div>
                                <p class="text-sm text-base-content/70">{{ $item->description }}</p>
                                <div class="card-actions justify-end">
                                    @if ($item->project_url)
                                        <a href="{{ $item->project_url }}" target="_blank" rel="noreferrer" class="btn btn-sm btn-primary">Open Project</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="carousel-item w-full">
                        <div class="alert">
                            <span>Add portfolio items from the CMS to populate this carousel.</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="space-y-4">
            <div>
                <h2 class="text-3xl font-semibold">Testimonials</h2>
                <p class="text-base-content/70">Customer trust blocks already support rating, avatar, position, and company.</p>
            </div>
            <div class="carousel w-full gap-4">
                @forelse ($testimonials as $testimonial)
                    <div class="carousel-item w-full max-w-md">
                        <div class="card w-full border border-base-300 bg-base-100 shadow-sm">
                            <div class="card-body">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="card-title text-lg">{{ $testimonial->name }}</h3>
                                        <p class="text-sm text-base-content/60">{{ $testimonial->position }} {{ $testimonial->company ? '· '.$testimonial->company : '' }}</p>
                                    </div>
                                    <div class="rating rating-sm">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <input type="radio" class="mask mask-star-2 bg-orange-400" disabled {{ $testimonial->rating === $i ? 'checked' : '' }} />
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-base-content/70">"{{ $testimonial->message }}"</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert">
                        <span>Testimonials will appear here once the table is populated.</span>
                    </div>
                @endforelse
            </div>
        </section>

        <section id="contact" class="grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-3xl">{{ $contact?->title ?? 'Contact Us' }}</h2>
                    <p class="text-base-content/70">
                        {{ $contact?->content ?? 'Store address, WhatsApp, maps embed, and support channels can be managed from settings or CMS sections.' }}
                    </p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-box bg-base-200 p-4">
                            <div class="text-sm text-base-content/60">Email</div>
                            <div class="font-medium">{{ $settings->get('contact_email', 'hello@example.com') }}</div>
                        </div>
                        <div class="rounded-box bg-base-200 p-4">
                            <div class="text-sm text-base-content/60">Phone</div>
                            <div class="font-medium">{{ $settings->get('contact_phone', '+62 000 0000 0000') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title">Kirim Pesan</h3>
                    <form action="{{ route('contact.store') }}" method="POST" class="grid gap-3">
                        @csrf
                        <div class="form-control">
                            <input type="text" name="name" value="{{ old('name') }}"
                                placeholder="Nama Anda" class="input input-bordered w-full @error('name') input-error @enderror" required>
                            @error('name')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>
                        <div class="form-control">
                            <input type="email" name="email" value="{{ old('email') }}"
                                placeholder="Email Anda" class="input input-bordered w-full @error('email') input-error @enderror" required>
                            @error('email')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>
                        <div class="form-control">
                            <input type="text" name="subject" value="{{ old('subject') }}"
                                placeholder="Subjek" class="input input-bordered w-full @error('subject') input-error @enderror" required>
                            @error('subject')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>
                        <div class="form-control">
                            <textarea name="message" rows="4"
                                placeholder="Pesan Anda..."
                                class="textarea textarea-bordered min-h-32 w-full @error('message') textarea-error @enderror"
                                required>{{ old('message') }}</textarea>
                            @error('message')
                                <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
