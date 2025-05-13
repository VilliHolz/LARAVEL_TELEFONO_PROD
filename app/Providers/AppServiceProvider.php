<?php

namespace App\Providers;

use App\Models\Branch;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
        View::composer('*', function ($view) {
            // Obtener todas las sucursales
            $sucursales = Branch::where('status', 'Activo')->get();            
            // Compartir las sucursales con todas las vistas
            $view->with('sucursales', $sucursales);
        });
    }
}
