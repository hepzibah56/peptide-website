<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('shop.config', function () {
            return config('shop');
        });
    }

    public function boot(): void
    {
        if (config('app.debug') && PHP_SAPI !== 'cli') {
            \Illuminate\Database\Eloquent\Model::preventLazyLoading();
        }

        view()->composer(['layouts.app', 'components.header'], function (View $view) {
            $count = 0;
            try {
                $count = app(CartService::class)->count();
            } catch (\Throwable) {
                $count = 0;
            }
            $view->with('cartCount', $count);
        });
    }
}