<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Menu;
use App\Http\Controllers\Controller;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Mengambil semua menu dan submenus untuk disertakan ke view tertentu
        View::composer('*', function ($view) {
            // Ambil data menu
            $menus = Menu::with('submenus')->orderBy('menu_sort', 'asc')->get();
            
            // Memanggil fungsi getCurrentUser dari Controller
            $controller = new Controller();
            $currentUser = $controller->getCurrentUser();
            $activeRole = session('roles', null);

            // Sertakan data menu dan user ke dalam semua view
            $view->with([
                'menus' => $menus,          // Menyertakan data menu
                'currentUser' => $currentUser, // Menyertakan data user yang sedang login
                'activeRole' => $activeRole
            ]);
        });
    }

    public function register()
    {
        //
    }
}
