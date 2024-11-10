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
        View::composer('*', function ($view) {
            $controller = new Controller();
            $currentUser = $controller->getCurrentUser();
            $activeRole = session('roles', null);
            $code_admin = 2;
            $code_superadmin = 1;
            $activeMenuId = session('activeMenu', null) ?? 1;

            $roleId = isset($activeRole['role_id']) ? $activeRole['role_id'] : null;

            $menus = Menu::with(['submenus', 'permissions' => function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            }])
            ->orderBy('menu_sort', 'asc')
            ->get();

            $activeMenuData = Menu::with(['permissions' => function ($query) use ($roleId) {
                    $query->where('role_id', $roleId);
                }])
                ->where('menu_id', $activeMenuId)
                ->first();

            // Menangani null untuk $activeMenuData dan permissions
            $activeMenu = [
                'can_read' => $activeMenuData && $activeMenuData->permissions->first() ? $activeMenuData->permissions->first()->can_read == 1 : false,
                'can_create' => $activeMenuData && $activeMenuData->permissions->first() ? $activeMenuData->permissions->first()->can_create == 1 : false,
                'can_update' => $activeMenuData && $activeMenuData->permissions->first() ? $activeMenuData->permissions->first()->can_update == 1 : false,
                'can_delete' => $activeMenuData && $activeMenuData->permissions->first() ? $activeMenuData->permissions->first()->can_delete == 1 : false,
            ];

            $view->with([
                'menus' => $menus,
                'currentUser' => $currentUser,
                'activeRole' => $activeRole,
                'code_admin' => $code_admin,
                'code_superadmin' => $code_superadmin,
                'activeMenu' => $activeMenu
            ]);
        });
    }

    public function register()
    {
        //
    }
}
