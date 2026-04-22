@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="text-3xl font-semibold">Shop Catalog</h1>
                <p class="text-base-content/70">Browse categories, filter products, and add items into the session-based cart.</p>
            </div>
            <a href="{{ route('cart.index') }}" class="btn btn-primary btn-sm">Open Cart</a>
        </div>

        <div class="grid gap-6 lg:grid-cols-[280px,1fr]">
            <aside class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">Filters</h2>
                    <form method="GET" action="{{ route('shop.index') }}" class="space-y-4">
                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">Search</span>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" class="input input-bordered w-full" placeholder="Product name or SKU">
                        </label>

                        <label class="form-control w-full">
                            <div class="label">
                                <span class="label-text">Category</span>
                            </div>
                            <select name="category" class="select select-bordered w-full">
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <div class="card-actions justify-end">
                            <a href="{{ route('shop.index') }}" class="btn btn-ghost">Reset</a>
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </div>
                    </form>
                </div>
            </aside>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($products as $product)
                    <div class="card border border-base-300 bg-base-100 shadow-sm">
                        <div class="card-body">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="card-title">{{ $product->name }}</h2>
                                    <p class="text-sm text-base-content/60">{{ $product->sku }}</p>
                                </div>
                                @if ($product->is_featured)
                                    <span class="badge badge-primary">Featured</span>
                                @endif
                            </div>

                            @if ($product->category)
                                <div class="badge badge-outline">{{ $product->category->name }}</div>
                            @endif

                            <p class="text-sm text-base-content/70">
                                {{ $product->short_description ?? \Illuminate\Support\Str::limit(strip_tags($product->description), 100) }}
                            </p>

                            <div class="mt-2 text-2xl font-semibold text-primary">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </div>

                            <div class="card-actions justify-between pt-3">
                                <a href="{{ route('shop.show', $product->slug) }}" class="btn btn-ghost btn-sm">Details</a>
                                <form method="POST" action="{{ route('cart.store') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert lg:col-span-2 xl:col-span-3">
                        <span>No products matched your filters.</span>
                    </div>
                @endforelse
            </div>
        </div>

        <div>
            {{ $products->links() }}
        </div>
    </div>
@endsection
