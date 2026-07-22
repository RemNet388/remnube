<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\Seccion; 
use App\Models\Categoria;

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
            $footer = Seccion::where('slug', 'footer')->first();
            $view->with('footer', $footer);
        });
        
        // Paginación con Bootstrap
        Paginator::useBootstrap();

        // Variables globales SOLO para el front
        View::composer('front.*', function ($view) {
            $view->with(
                'categorias',
                Categoria::orderBy('nombre')->get()
            );
        });
    }
}
