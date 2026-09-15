<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ Pagination default seluruh aplikasi → tampilan custom Graniva (mewah & profesional)
        Paginator::defaultView('pagination.graniva');
        Paginator::defaultSimpleView('pagination.graniva');

        // Fallback aman biar markup Bootstrap yang dipakai (bukan Tailwind SVG raksasa)
        Paginator::useBootstrapFive();
    }
}