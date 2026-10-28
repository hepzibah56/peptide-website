<header class="bg-white border-b border-borosilicate sticky top-0 z-40">
    <div class="max-w-1280 mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-fjord-ink font-display font-bold text-xl">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('shop.name') }}" class="h-9 w-auto">
            <span>{{ config('shop.name') }}</span>
        </a>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-fjord-ink">
            <a href="{{ route('products.index') }}" class="hover:text-tide">{{ trans('nav.shop') }}</a>
            <a href="{{ route('home') }}#categories" class="hover:text-tide">{{ trans('nav.categories') }}</a>
            <a href="{{ route('home') }}#delivery" class="hover:text-tide">{{ trans('nav.delivery') }}</a>
            <a href="{{ route('contact.index') }}" class="hover:text-tide">{{ trans('nav.contact') }}</a>
        </nav>

        <div class="flex items-center gap-2">
            <form action="{{ route('products.index') }}" method="GET" class="hidden sm:flex items-center">
                <input type="search" name="q" value="{{ request('q') ?? '' }}"
                       placeholder="{{ trans('nav.search') }}"
                       class="text-sm px-3 py-1.5 border border-borosilicate bg-frost focus:border-glacier focus:ring-1 focus:ring-glacier outline-none w-40 focus:w-56 transition-all">
            </form>

            <select onchange="window.location.href=this.value"
                    aria-label="{{ trans('nav.language') }}"
                    class="text-sm px-2 py-1.5 border border-borosilicate bg-frost rounded focus:outline-none focus:border-glacier">
                <option value="{{ route('home', ['locale' => 'nb']) }}" @selected(app()->getLocale()==='nb')>{{ trans('nav.nb') }}</option>
                <option value="{{ route('home', ['locale' => 'en']) }}" @selected(app()->getLocale()==='en')>{{ trans('nav.en') }}</option>
            </select>

            <button id="cart-open-btn" aria-expanded="false" aria-controls="cart-drawer" aria-label="{{ trans('nav.cart') }} ({{ $cartCount ?? 0 }})"
                    class="relative flex items-center gap-1.5 px-3 py-1.5 bg-glacier text-fjord-ink font-semibold text-sm rounded hover:bg-tide hover:text-white transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293A1 1 0 005 17h10a1 1 0 00.707-.293L19 13m-8 8a1 1 0 100-2 1 1 0 010 2zm8-1a1 1 0 11-2 0 1 1 0 012 0"/>
                </svg>
                <span class="hidden sm:inline">{{ trans('nav.cart') }}</span>
                @if($cartCount ?? 0)
                    <span class="absolute -top-1 -right-1 w-5 h-5 flex items-center justify-center text-[11px] font-bold text-fjord-ink bg-amber-glass rounded-full">{{ $cartCount }}</span>
                @endif
            </button>
        </div>
    </div>
</header>