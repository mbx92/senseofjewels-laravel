@extends('layouts.app')

@section('content')
    @php
        $hero = $sections->get('hero');
        $about = $sections->get('about');
        $contact = $sections->get('contact');
    @endphp

    <div class="space-y-10">
        {{-- Hero Section: 3-panel editorial layout --}}
        @php
            $h = fn(string $key, string $default = '') => ($hero?->settings[$key] ?? null) ?: $default;
            $panel1Bg = $hero?->image_url
                ? 'background-image:url(\''.e($hero->image_url).'\');background-size:cover;background-position:center;'
                : 'background:linear-gradient(160deg,#3d2b1f 0%,#8b7355 60%,#bfa054 100%);';
            $panel2Bg = $h('banner1_image')
                ? 'background-image:url(\''.e($h('banner1_image')).'\');background-size:cover;background-position:center;'
                : 'background:linear-gradient(135deg,#f5ede0 0%,#e8d5b7 100%);';
            $panel3Bg = $h('banner2_image')
                ? 'background-image:url(\''.e($h('banner2_image')).'\');background-size:cover;background-position:center;'
                : 'background:linear-gradient(135deg,#ede8df 0%,#ddd0b8 100%);';
        @endphp
        <section style="display:grid;grid-template-columns:3fr 2fr;min-height:580px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,.18);">

            {{-- Panel 1: Campaign / Main Hero --}}
            <div style="position:relative;display:flex;flex-direction:column;justify-content:space-between;padding:3rem;{{ $panel1Bg }}">
                <div style="position:absolute;inset:0;background:rgba(0,0,0,.40);"></div>
                {{-- Top: season badge --}}
                <div style="position:relative;z-index:10;">
                    <p style="font-size:10px;letter-spacing:.35em;text-transform:uppercase;color:rgba(255,255,255,.50);">{{ $h('season_badge', 'NEW SEASON ' . date('Y')) }}</p>
                </div>
                {{-- Bottom: main content --}}
                <div style="position:relative;z-index:10;">
                    <p style="font-size:10px;letter-spacing:.25em;text-transform:uppercase;color:rgba(255,255,255,.60);margin-bottom:1rem;">{{ $h('eyebrow', 'ARTISAN JEWELRY · BALI') }}</p>
                    <h1 class="display-font" style="font-size:clamp(2.5rem,5vw,4.5rem);font-weight:300;line-height:1.05;color:#fff;">
                        {{ $hero?->title ?? 'Timeless' }}<br>
                        <em style="font-style:italic;">{{ $hero?->subtitle ?? 'Elegance' }}</em>
                    </h1>
                    <p style="margin-top:1.25rem;font-size:.875rem;color:rgba(255,255,255,.65);max-width:20rem;line-height:1.6;">
                        {{ $hero?->content ?? 'Handcrafted fine jewelry designed for the modern everyday.' }}
                    </p>
                    <div style="margin-top:2rem;">
                        <a href="{{ $hero?->cta_url ?? route('shop.index') }}"
                           style="display:inline-flex;align-items:center;gap:.75rem;border:1px solid rgba(255,255,255,.50);color:#fff;font-size:10px;letter-spacing:.3em;text-transform:uppercase;padding:.875rem 1.75rem;text-decoration:none;transition:background .3s,color .3s;"
                           onmouseover="this.style.background='#fff';this.style.color='#1a1a1a';"
                           onmouseout="this.style.background='transparent';this.style.color='#fff';">
                            {{ $hero?->cta_text ?? 'SHOP COLLECTION' }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right column: 2 stacked banners --}}
            <div style="display:flex;flex-direction:column;">

                {{-- Panel 2: Product Banner (top right) --}}
                <div style="position:relative;display:flex;align-items:center;padding:2.5rem;flex:1;{{ $panel2Bg }}">
                    @if($h('banner1_image'))<div style="position:absolute;inset:0;background:rgba(0,0,0,.30);"></div>@endif
                    <div style="position:relative;z-index:10;">
                        <p style="font-size:9px;letter-spacing:.3em;text-transform:uppercase;color:{{ $h('banner1_image') ? 'rgba(255,255,255,.60)' : 'rgba(0,0,0,.45)' }};">
                            {{ $h('banner1_label', 'SELECTED COLLECTION') }}
                        </p>
                        <h2 class="display-font" style="font-size:clamp(1.75rem,3vw,2.5rem);font-weight:300;margin-top:.5rem;color:{{ $h('banner1_image') ? '#fff' : 'inherit' }};">
                            {{ $h('banner1_title', 'Emas & Perak') }}
                        </h2>
                        <p class="display-font" style="font-size:1.25rem;font-style:italic;margin-top:.25rem;color:{{ $h('banner1_image') ? '#fcd34d' : '#bfa054' }};">
                            {{ $h('banner1_subtitle', 'Artisan Bali') }}
                        </p>
                        @if($h('banner1_cta_text'))
                            <a href="{{ $h('banner1_cta_url', route('shop.index')) }}"
                               style="display:inline-flex;align-items:center;border:1px solid {{ $h('banner1_image') ? 'rgba(255,255,255,.50)' : 'rgba(0,0,0,.35)' }};color:{{ $h('banner1_image') ? '#fff' : 'inherit' }};font-size:9px;letter-spacing:.3em;text-transform:uppercase;padding:.625rem 1.25rem;margin-top:1.25rem;text-decoration:none;">
                                {{ $h('banner1_cta_text') }}
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Panel 3: Category Banner (bottom right) --}}
                <div style="position:relative;display:flex;align-items:center;padding:2.5rem;flex:1;border-top:1px solid rgba(0,0,0,.08);{{ $panel3Bg }}">
                    @if($h('banner2_image'))<div style="position:absolute;inset:0;background:rgba(0,0,0,.30);"></div>@endif
                    <div style="position:relative;z-index:10;">
                        <p style="font-size:9px;letter-spacing:.3em;text-transform:uppercase;color:{{ $h('banner2_image') ? 'rgba(255,255,255,.60)' : 'rgba(0,0,0,.45)' }};">
                            {{ $h('banner2_label', 'EST. 2019') }}
                        </p>
                        <h2 class="display-font" style="font-size:clamp(1.75rem,3vw,2.5rem);font-weight:300;margin-top:.5rem;color:{{ $h('banner2_image') ? '#fff' : 'inherit' }};">
                            {{ $h('banner2_title', 'Cincin &') }}<br>
                            <em style="font-style:italic;color:{{ $h('banner2_image') ? '#fcd34d' : '#bfa054' }};">
                                {{ $h('banner2_subtitle', 'Kalung Pilihan') }}
                            </em>
                        </h2>
                        @if($h('banner2_cta_text'))
                            <a href="{{ $h('banner2_cta_url', route('shop.index')) }}"
                               style="display:inline-flex;align-items:center;border:1px solid {{ $h('banner2_image') ? 'rgba(255,255,255,.50)' : 'rgba(0,0,0,.35)' }};color:{{ $h('banner2_image') ? '#fff' : 'inherit' }};font-size:9px;letter-spacing:.3em;text-transform:uppercase;padding:.625rem 1.25rem;margin-top:1.25rem;text-decoration:none;">
                                {{ $h('banner2_cta_text') }}
                            </a>
                        @endif
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
