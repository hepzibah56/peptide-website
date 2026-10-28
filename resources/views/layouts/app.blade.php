<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('shop.name'))</title>
    <meta name="description" content="@yield('meta_description', 'Norges Pure Peps - Norwegian laboratory supply store.')">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    @yield('head')
</head>
<body class="bg-frost text-fjord-ink font-sans antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 bg-glacier text-fjord-ink px-3 py-2 rounded">Hopp til hovedinnhold</a>

    <x-header />

    <main id="main-content">
        @yield('content')
    </main>

    <x-footer />

    <x-cookie-consent />

    @yield('scripts')
</body>
</html>