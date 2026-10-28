@extends('layouts.app')

@section('title', trans('nav.shop'))

@section('content')
<div class="max-w-1280 mx-auto px-4 sm:px-6">
    <section class="bg-deep-water text-white rounded-lg p-8 mb-8">
        <p class="text-glacier text-xs font-semibold tracking-[0.2em] uppercase mb-2">{{ trans('shop.eyebrow') }}</p>
        <h1 class="font-display text-4xl">{{ trans('shop.headline') }}</h1>
        <p class="text-[#cfe3f5] text-sm mt-2">{{ $products->total() }} {{ trans('shop.results') }}</p>
    </section>

    <div class="lg:grid lg:grid-cols-[260px_1fr] gap-8">
        <aside class="hidden lg:block">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-6">
                <div>
                    <label for="q" class="block text-sm font-semibold mb-2">{{ trans('shop.search') }}</label>
                    <input type="search" name="q" id="q" value="{{ request('q') ?? '' }}"
                           class="w-full px-3 py-2 text-sm border border-borosilicate bg-frost focus:border-glacier focus:ring-1 focus:ring-glacier outline-none">
                </div>

                <div>
                    <span class="block text-sm font-semibold mb-2">{{ trans('shop.sort') }}</span>
                    <select name="sort" onchange="this.form.submit()"
                            class="w-full px-3 py-2 text-sm border border-borosilicate bg-frost focus:border-glacier outline-none">
                        <option value="newest" @selected(request('sort')==='newest')>{{ trans('shop.sort_newest') }}</option>
                        <option value="price_asc" @selected(request('sort')==='price_asc')>{{ trans('shop.sort_price_asc') }}</option>
                        <option value="price_desc" @selected(request('sort')==='price_desc')>{{ trans('shop.sort_price_desc') }}</option>
                        <option value="name" @selected(request('sort')==='name')>{{ trans('shop.sort_name') }}</option>
                    </select>
                </div>

                <div>
                    <span class="block text-sm font-semibold mb-2">{{ trans('shop.category') }}</span>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="category" value="" @selected(!request('category')) class="accent-tide">
                            {{ trans('shop.all') }}
                        </label>
                        @foreach(\App\Models\Category::where('is_active', true)->orderBy('sort')->get() as $cat)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="radio" name="category" value="{{ $cat->slug }}" @selected(request('category')===$cat->slug) class="accent-tide">
                                {{ $cat->name }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="w-full px-4 py-2 bg-glacier text-fjord-ink font-semibold text-sm rounded hover:bg-white transition">{{ trans('shop.apply') }}</button>
            </form>
        </aside>

        <div>
            @if($products->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        <x-product-tile :product="$product" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $products->links() }}</div>
            @else
                <div class="bg-white border border-borosilicate rounded-lg p-10 text-center">
                    <h3 class="font-display text-2xl">{{ trans('shop.empty') }}</h3>
                    <p class="text-[#63758a] text-sm mt-2">{{ trans('shop.empty_sub') }}</p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-4 px-5 py-2 bg-glacier text-fjord-ink font-semibold text-sm rounded hover:bg-white transition">{{ trans('shop.reset') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>