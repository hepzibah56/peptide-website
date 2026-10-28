<div id="cart-drawer" class="fixed inset-0 z-50" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 transition-opacity" onclick="document.getElementById('cart-close-btn').click()"></div>
    <aside class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-xl flex flex-col translate-x-full transition-transform duration-200">
        <div class="flex items-center justify-between p-4 border-b border-borosilicate">
            <h2 class="font-display text-xl">{{ trans('cart.title') }}</h2>
            <button id="cart-close-btn" aria-label="{{ trans('cart.close') }}" class="w-9 h-9 flex items-center justify-center rounded hover:bg-frost">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div id="cart-items" class="flex-1 overflow-y-auto p-4 space-y-4">
            @forelse($items as $item)
                <div class="flex gap-3 items-start border-b border-borosilicate pb-4">
                    <div class="w-16 h-16 rounded bg-glacier-mist flex items-center justify-center flex-shrink-0">
                        @if($item['product']->image)
                            <img src="{{ asset('storage/'.$item['product']->image) }}" alt="" class="w-10 h-10 object-contain">
                        @else
                            <img src="{{ asset('images/logo.png') }}" alt="" class="w-8 h-8 object-contain opacity-70">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm leading-snug">{{ $item['name'] }}</p>
                        <p class="text-xs text-[#63758a] tabular">{{ number_format($item['price'], 0, ',', ' ') }} kr/stk</p>
                        <div class="qty-stepper flex items-center gap-2 mt-2">
                            <button type="button" class="qty-dec w-7 h-7 rounded border border-borosilicate hover:bg-frost">−</button>
                            <input type="number" min="1" max="{{ $item['product']->stock ?? 99 }}" value="{{ $item['quantity'] }}"
                                   data-id="{{ $item['product']->id }}"
                                   class="qty-input w-10 h-7 text-center text-sm border border-borosilicate tabular">
                            <button type="button" class="qty-inc w-7 h-7 rounded border border-borosilicate hover:bg-frost">+</button>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('cart.remove', $item['product']->id) }}" class="self-start">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-error hover:underline">{{ trans('cart.remove') }}</button>
                    </form>
                </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-[#63758a]">{{ trans('cart.empty') }}</p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-3 text-sm font-semibold text-tide">{{ trans('cart.keep_shopping') }}</a>
                </div>
            @endforelse
        </div>

        @if(!empty($items))
            <div class="border-t border-borosilicate p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-[#63758a]">{{ trans('cart.subtotal') }}</span>
                    <span class="font-semibold tabular">{{ number_format($subtotal, 0, ',', ' ') }} kr</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-[#63758a]">{{ trans('cart.vat') }}</span>
                    <span class="tabular">{{ number_format(round($subtotal * 0.25), 0, ',', ' ') }} kr</span>
                </div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t border-borosilicate">
                    <span>{{ trans('cart.total') }}</span>
                    <span class="tabular">{{ number_format(round($subtotal * 1.25), 0, ',', ' ') }} kr</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="block w-full text-center px-4 py-3 bg-glacier text-fjord-ink font-bold rounded hover:bg-white transition mt-3">{{ trans('cart.checkout') }}</a>
            </div>
        @endif
    </aside>
</div>

<script>
    const updateUrl = '{{ route('cart.update', 0) }}';
    const csrf = '{{ csrf_token() }}';
    document.querySelectorAll('#cart-items .qty-input').forEach(input => {
        input.addEventListener('change', () => {
            const qty = Math.max(1, parseInt(input.value, 10) || 1);
            fetch(updateUrl.replace('0', input.dataset.id), {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ quantity: qty })
            }).then(r => r.json()).then(data => {
                if (data.ok) location.reload();
            });
        });
    });
    document.querySelectorAll('#cart-items .qty-dec, #cart-items .qty-inc').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('.qty-input');
            input.value = btn.classList.contains('qty-inc') ? parseInt(input.value, 10) + 1 : Math.max(1, parseInt(input.value, 10) - 1);
            input.dispatchEvent(new Event('change'));
        });
    });
</script>