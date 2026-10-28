@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-1280 mx-auto px-4 sm:px-6">
    <nav class="text-sm text-[#63758a] mb-6">
        <a href="{{ route('products.index') }}">{{ trans('nav.shop') }}</a>
        <span class="mx-2">/</span>
        <span class="text-fjord-ink">{{ $product->name }}</span>
    </nav>

    <div class="grid md:grid-cols-[1fr_1.2fr] gap-8">
        <div class="bg-white border border-borosilicate rounded-lg aspect-[4/5] flex items-center justify-center p-8">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
            @else
                <img src="{{ asset('images/logo.png') }}" alt="" class="w-24 h-24 object-contain opacity-70">
            @endif
        </div>

        <div>
            <p class="text-xs text-tide font-semibold uppercase tracking-wider mb-2">{{ $product->category ?? trans('home.catalogue') }}</p>
            <h1 class="font-display text-4xl mb-4">{{ $product->name }}</h1>
            <p class="text-[#63758a] leading-relaxed mb-6">{{ $product->description ?? trans('product.no_info') }}</p>

            <div class="flex items-baseline gap-3 mb-6">
                <p class="font-display text-4xl tabular">{{ number_format($product->price, 0, ',', ' ') }} kr</p>
                <span class="text-sm text-[#63758a]">{{ trans('product.incl_vat') }}</span>
            </div>

            <div class="mb-6">
                @if($product->track_stock && $product->stock === 0)
                    <p class="text-error font-semibold text-sm">{{ trans('product.out_of_stock') }}</p>
                    <a href="mailto:{{ config('shop.email') }}" class="text-sm text-tide font-semibold">{{ trans('product.contact') }}</a>
                @else
                    <div class="flex items-center gap-4">
                        <div class="qty-stepper flex items-center border border-borosilicate rounded">
                            <button type="button" class="qty-dec w-9 h-9 flex items-center justify-center text-fjord-ink hover:bg-glacier-mist">−</button>
                            <input type="number" min="1" max="{{ $product->stock ?? 99 }}" value="1" class="qty-input w-12 h-9 text-center border-x border-borosilicate focus:outline-none tabular">
                            <button type="button" class="qty-inc w-9 h-9 flex items-center justify-center text-fjord-ink hover:bg-glacier-mist">+</button>
                        </div>
                        <span class="text-sm text-[#63758a]">
                            @if($product->track_stock)
                                {{ trans('product.stock') }}: {{ $product->stock }}
                            @else
                                {{ trans('product.unlimited') }}
                            @endif
                        </span>
                    </div>

                    <form method="POST" action="{{ route('cart.add', $product->id) }}" class="mt-5 flex items-center gap-3">
                        @csrf
                        <input type="hidden" name="quantity" id="qty-hidden" value="1">
                        <button type="submit" class="px-6 py-3 bg-glacier text-fjord-ink font-bold rounded hover:bg-white transition">{{ trans('product.add_to_cart') }}</button>
                        <span id="cart-msg" class="text-sm text-success font-semibold" style="display:none"></span>
                    </form>
                @endif
            </div>

            <div class="border-t border-borosilicate pt-5 text-sm text-[#63758a] space-y-1">
                <p><span class="text-fjord-ink font-semibold">{{ trans('product.sku') }}:</span> {{ $product->sku ?? '—' }}</p>
                <p><span class="text-fjord-ink font-semibold">{{ trans('product.storage') }}:</span> {{ $product->storage ?? '—' }}</p>
                <p><span class="text-fjord-ink font-semibold">{{ trans('product.lot') }}:</span> {{ $product->lot ?? '—' }}</p>
            </div>
        </div>
    </div>

    @if($related->isNotEmpty())
        <section class="mt-14">
            <h2 class="font-display text-2xl mb-5">{{ trans('product.related') }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($related as $p)
                    <x-product-tile :product="$p" />
                @endforeach
            </div>
        </section>
    @endif
</div>

<x-cart-drawer />