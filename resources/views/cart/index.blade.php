@extends('layouts.app')

@section('title', trans('cart.title'))

@section('content')
<div class="max-w-1280 mx-auto px-4 sm:px-6">
    <h1 class="font-display text-4xl mb-8">{{ trans('cart.title') }}</h1>

    @if(!empty($items))
        <div class="bg-white border border-borosilicate rounded-lg divide-y divide-borosilicate">
            @foreach($items as $item)
                <div class="flex items-center gap-4 p-4">
                    <div class="w-20 h-20 rounded bg-glacier-mist flex items-center justify-center flex-shrink-0">
                        @if($item['product']->image)
                            <img src="{{ asset('storage/'.$item['product']->image) }}" alt="" class="w-12 h-12 object-contain">
                        @else
                            <img src="{{ asset('images/logo.png') }}" alt="" class="w-9 h-9 object-contain opacity-70">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold">{{ $item['name'] }}</p>
                        <p class="text-sm text-[#63758a] tabular">{{ number_format($item['price'], 0, ',', ' ') }} kr/stk</p>
                    </div>
                    <form method="POST" action="{{ route('cart.update', $item['product']->id) }}">
                        @csrf
                        @method('PATCH')
                        <input type="number" name="quantity" min="1" value="{{ $item['quantity'] }}" class="w-16 h-9 text-center border border-borosilicate tabular">
                    </form>
                    <p class="font-bold tabular w-24 text-right">{{ number_format($item['line_total'], 0, ',', ' ') }} kr</p>
                    <form method="POST" action="{{ route('cart.remove', $item['product']->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-error text-sm hover:underline">{{ trans('cart.remove') }}</button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="grid md:grid-cols-[1fr_320px] gap-8 mt-8">
            <div>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-tide hover:underline">{{ trans('cart.keep_shopping') }}</a>
            </div>
            <div class="bg-white border border-borosilicate rounded-lg p-6 h-fit">
                <h2 class="font-display text-xl mb-4">{{ trans('cart.summary') }}</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-[#63758a]">{{ trans('cart.subtotal') }}</span><span class="font-semibold tabular">{{ number_format($subtotal, 0, ',', ' ') }} kr</span></div>
                    <div class="flex justify-between"><span class="text-[#63758a]">{{ trans('cart.vat') }}</span><span class="tabular">{{ number_format(round($subtotal * 0.25), 0, ',', ' ') }} kr</span></div>
                    <div class="flex justify-between font-bold text-lg pt-3 border-t border-borosilicate"><span>{{ trans('cart.total') }}</span><span class="tabular">{{ number_format(round($subtotal * 1.25), 0, ',', ' ') }} kr</span></div>
                </div>
                <a href="{{ route('checkout.index') }}" class="block w-full text-center mt-5 px-4 py-3 bg-glacier text-fjord-ink font-bold rounded hover:bg-white transition">{{ trans('cart.checkout') }}</a>
            </div>
        </div>
    @else
        <div class="bg-white border border-borosilicate rounded-lg p-12 text-center">
            <p class="text-[#63758a]">{{ trans('cart.empty') }}</p>
            <a href="{{ route('products.index') }}" class="inline-block mt-4 px-5 py-2 bg-glacier text-fjord-ink font-semibold text-sm rounded hover:bg-white transition">{{ trans('cart.keep_shopping') }}</a>
        </div>
    @endif
</div>

<x-cart-drawer />