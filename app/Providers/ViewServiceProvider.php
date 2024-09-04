<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Menu;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Mengambil semua menu dan submenus untuk disertakan ke view tertentu
        View::composer('*', function ($view) {
            $menus = Menu::with('submenus')->orderBy('menu_sort', 'asc')->get();
            $view->with('menus', $menus); // Menyertakan data ke view
        });
    }

    public function register()
    {
        //
    }
}
