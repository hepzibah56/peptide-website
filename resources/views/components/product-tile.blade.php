<a href="{{ route('products.show', $product->slug) }}" class="group block bg-white border border-borosilicate rounded-lg overflow-hidden hover:shadow-md hover:border-glacier transition">
    <div class="aspect-square bg-glacier-mist flex items-center justify-center">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-4">
        @else
            <img src="{{ asset('images/logo.png') }}" alt="" class="w-16 h-16 object-contain opacity-70">
        @endif
    </div>
    <div class="p-4">
        <p class="text-xs text-tide font-semibold uppercase tracking-wider mb-1">{{ $product->category ?? trans('home.catalogue') }}</p>
        <h3 class="font-display text-lg leading-snug group-hover:text-tide transition">{{ $product->name }}</h3>
        <p class="text-sm text-[#63758a] mt-1 line-clamp-2">{{ $product->summary ?? \Illuminate\Support\Str::limit($product->description ?? '', 80) }}</p>
        <div class="flex items-center justify-between mt-3">
            <p class="font-bold text-fjord-ink tabular">{{ number_format($product->price, 0, ',', ' ') }} kr</p>
            @if($product->track_stock && $product->stock <= 10 && $product->stock > 0)
                <span class="text-xs text-amber-glass font-semibold">{{ trans('product.low_stock') }}</span>
            @elseif($product->track_stock && $product->stock === 0)
                <span class="text-xs text-error font-semibold">{{ trans('product.out_of_stock') }}</span>
            @endif
        </div>
    </div>
</a>