<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('locale') ?? $request->cookie('locale', config('app.locale', 'nb'));

        if (! in_array($locale, ['nb', 'en'])) {
            $locale = 'nb';
        }

        app()->setLocale($locale);

        return $next($request)->cookie('locale', $locale, 60 * 24 * 365);
    }
}