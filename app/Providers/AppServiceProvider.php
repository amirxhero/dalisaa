<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\CartService;
use App\Support\NavigationMenu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The header, mobile menu and category sheet are part of the base
        // layout and rendered on every page, so share the mega-menu data
        // with them instead of requiring every controller to pass it.
        View::composer(
            ['partials.site-header', 'partials.mobile-menu', 'partials.category-sheet'],
            fn ($view) => $view->with('megaMenu', NavigationMenu::items()),
        );

        // The cart drawer + header/nav cart badges are part of every page,
        // so share the current (session or user) cart with them here.
        View::composer(
            ['partials.cart-drawer', 'partials.site-header', 'partials.mobile-bottom-nav'],
            fn ($view) => $view->with('cart', app(CartService::class)->currentCart()),
        );

        // Contact/social details live in site settings and are referenced by
        // the header, footer, mobile menu, contact page, etc. Share them with
        // every view so nothing hardcodes a phone number, email or handle.
        View::share('siteSettings', [
            'contact_address'  => Setting::get('contact_address', ''),
            'contact_phone'    => Setting::get('contact_phone', ''),
            'contact_email'    => Setting::get('contact_email', ''),
            'social_instagram' => Setting::get('social_instagram', ''),
            'social_telegram'  => Setting::get('social_telegram', ''),
            'social_whatsapp'  => Setting::get('social_whatsapp', ''),
        ]);
    }
}
