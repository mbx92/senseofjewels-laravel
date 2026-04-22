@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <div class="breadcrumbs text-sm">
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('shop.index') }}">Shop</a></li>
                <li>{{ $product->name }}</li>
            </ul>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr,1.1fr]">
            <div class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body">
                    <h1 class="card-title text-3xl">{{ $product->name }}</h1>
                    <div class="flex flex-wrap gap-2">
                        <span class="badge badge-outline">{{ $product->sku }}</span>
                        @if ($product->category)
                            <span class="badge badge-primary badge-outline">{{ $product->category->name }}</span>
                        @endif
                    </div>
                    <p class="text-base-content/70">{{ $product->short_description }}</p>
                    <div class="text-4xl font-bold text-primary">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>
                    <div class="stats stats-vertical border border-base-300 bg-base-200 shadow-none lg:stats-horizontal">
                        <div class="stat px-4 py-3">
                            <div class="stat-title">Stock</div>
                            <div class="stat-value text-lg">{{ $product->stock }}</div>
                        </div>
                        <div class="stat px-4 py-3">
                            <div class="stat-title">Min Alert</div>
                            <div class="stat-value text-lg">{{ $product->min_stock_alert }}</div>
                        </div>
                        <div class="stat px-4 py-3">
                            <div class="stat-title">Weight</div>
                            <div class="stat-value text-lg">{{ $product->weight ? $product->weight.' g' : 'N/A' }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('cart.store') }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <label class="form-control w-28">
                            <div class="label">
                                <span class="label-text">Qty</span>
                            </div>
                            <input type="number" name="quantity" min="1" value="1" class="input input-bordered">
                        </label>
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Description</h2>
                        <p class="text-base-content/70">{{ $product->description ?: 'Detailed product content can be managed from the CMS admin.' }}</p>
                    </div>
                </div>

                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title">Specifications</h2>
                        @if (! empty($product->specifications))
                            <div class="overflow-x-auto">
                                <table class="table table-sm">
                                    <tbody>
                                        @foreach ($product->specifications as $label => $value)
                                            <tr>
                                                <th class="w-40">{{ \Illuminate\Support\Str::headline($label) }}</th>
                                                <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert">
                                <span>No specifications added yet.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h2 class="text-2xl font-semibold">Related Products</h2>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @forelse ($relatedProducts as $related)
                    <div class="card border border-base-300 bg-base-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title text-lg">{{ $related->name }}</h3>
                            <p class="text-sm text-base-content/70">{{ $related->short_description }}</p>
                            <div class="card-actions justify-between pt-2">
                                <span class="font-semibold text-primary">Rp {{ number_format($related->price, 0, ',', '.') }}</span>
                                <a href="{{ route('shop.show', $related->slug) }}" class="btn btn-ghost btn-sm">View</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert xl:col-span-4">
                        <span>More products will appear here once the catalog grows.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
