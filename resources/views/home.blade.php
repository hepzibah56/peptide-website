@extends('layouts.app')

@section('title', 'Norges Pure Peps')

@section('content')
<div class="max-w-1280 mx-auto px-4 sm:px-6">
    <section class="relative overflow-hidden rounded-lg bg-deep-water text-white mt-6 mb-10">
        <div class="grid md:grid-cols-2">
            <div class="p-8 sm:p-12 flex flex-col justify-center">
                <p class="text-glacier text-xs font-semibold tracking-[0.2em] uppercase mb-4">{{ trans('home.eyebrow') }}</p>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl leading-[1.05] mb-4">{{ trans('home.headline') }}</h1>
                <p class="text-[#cfe3f5] text-lg max-w-md mb-8 leading-relaxed">{{ trans('home.sub') }}</p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 bg-glacier text-fjord-ink font-bold rounded hover:bg-white transition">{{ trans('home.explore') }}</a>
                    <a href="#categories" class="inline-flex items-center px-6 py-3 border border-white/40 text-white font-semibold rounded hover:bg-white/10 transition">{{ trans('home.categories') }}</a>
                </div>
            </div>
            <div class="hidden md:flex items-center justify-center bg-gradient-to-br from-deep-water to-[#1a4a6b] min-h-[320px]">
                <img src="{{ asset('images/logo.png') }}" alt="" class="w-40 h-40 object-contain opacity-90">
            </div>
        </div>
    </section>

    <section id="categories" class="mb-12">
        <div class="flex items-end justify-between mb-6">
            <div>
                <p class="text-tide text-xs font-semibold tracking-[0.2em] uppercase mb-1">{{ trans('home.categories_eyebrow') }}</p>
                <h2 class="font-display text-3xl">{{ trans('home.categories_head') }}</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-tide hover:underline">{{ trans('home.all') }}</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($categories as $cat)
                <a href="{{ route('products.category', $cat->slug) }}" class="group block bg-white border border-borosilicate rounded-lg p-6 hover:shadow-md hover:border-glacier transition">
                    <div class="w-10 h-10 rounded-full bg-glacier-mist flex items-center justify-center mb-4 group-hover:bg-glacier transition">
                        <span class="w-3 h-3 rounded-full bg-tide"></span>
                    </div>
                    <h3 class="font-display text-xl group-hover:text-tide transition">{{ $cat->name }}</h3>
                    <p class="text-sm text-[#63758a] mt-1 line-clamp-2">{{ $cat->description ?? trans('home.coming_soon') }}</p>
                </a>
            @endforeach
        </div>
    </section>

    <section id="products" class="mb-12">
        <div class="flex items-end justify-between mb-6">
            <div>
                <p class="text-tide text-xs font-semibold tracking-[0.2em] uppercase mb-1">{{ trans('home.featured_eyebrow') }}</p>
                <h2 class="font-display text-3xl">{{ trans('home.featured_head') }}</h2>
            </div>
        </div>

        @if($featured->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($featured as $product)
                    <x-product-tile :product="$product" />
                @endforeach
            </div>
        @else
            <div class="bg-white border border-borosilicate rounded-lg p-8 text-center">
                <h3 class="font-display text-xl">{{ trans('home.catalogue_preparing') }}</h3>
                <p class="text-[#63758a] text-sm mt-1">{{ trans('home.catalogue_preparing_sub') }}</p>
            </div>
        @endif
    </section>

    <section id="delivery" class="grid sm:grid-cols-3 gap-4 mb-12">
        <div class="bg-white border border-borosilicate rounded-lg p-6">
            <h3 class="font-display text-lg mb-2">{{ trans('home.delivery_title') }}</h3>
            <p class="text-sm text-[#63758a]">{{ trans('home.delivery_text') }}</p>
        </div>
        <div class="bg-white border border-borosilicate rounded-lg p-6">
            <h3 class="font-display text-lg mb-2">{{ trans('home.payment_title') }}</h3>
            <p class="text-sm text-[#63758a]">{{ trans('home.payment_text') }}</p>
        </div>
        <div class="bg-white border border-borosilicate rounded-lg p-6">
            <h3 class="font-display text-lg mb-2">{{ trans('home.support_title') }}</h3>
            <p class="text-sm text-[#63758a]">{{ trans('home.support_text') }}</p>
            <a href="mailto:{{ config('shop.email') }}" class="text-sm font-semibold text-tide mt-2 inline-block">{{ trans('home.contact') }}</a>
        </div>
    </section>
</section>