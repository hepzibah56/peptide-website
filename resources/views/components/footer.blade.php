<footer class="bg-deep-water text-[#d6e6f7]">
    <div class="max-w-1280 mx-auto px-4 sm:px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
            <h3 class="text-white font-display text-lg mb-3">{{ config('shop.name') }}</h3>
            <p class="text-sm leading-relaxed">{{ trans('footer.about') }}</p>
            <p class="text-sm mt-2"><a href="mailto:{{ config('shop.email') }}">{{ config('shop.email') }}</a></p>
        </div>
        <div>
            <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-3">{{ trans('footer.customer') }}</h4>
            <div class="flex flex-col gap-2 text-sm">
                <a href="{{ route('products.index') }}">{{ trans('footer.shop') }}</a>
                <a href="{{ route('cart.index') }}">{{ trans('footer.cart') }}</a>
                <a href="{{ route('checkout.index') }}">{{ trans('footer.checkout') }}</a>
                <a href="{{ route('contact.index') }}">{{ trans('footer.contact') }}</a>
            </div>
        </div>
        <div>
            <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-3">{{ trans('footer.shipping') }}</h4>
            <div class="flex flex-col gap-2 text-sm">
                <a href="{{ config('shop.shipping_posten_link') }}" target="_blank" rel="noopener noreferrer">{{ trans('footer.posten') }}</a>
                <a href="mailto:{{ config('shop.email') }}">{{ trans('footer.support') }}</a>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="max-w-1280 mx-auto px-4 sm:px-6 py-4 text-xs text-[#9fb3c4]">
            &copy; {{ date('Y') }} {{ config('shop.name') }}. {{ trans('footer.rights') }}
        </div>
    </div>
</footer>