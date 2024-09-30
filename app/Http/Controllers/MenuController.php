<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Role;
use App\Models\Submenu;
use App\Models\MenuType;
use App\Models\RolePermission;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function index()
    {
        $listMenu = Menu::with('submenus', 'menuType')
            ->orderBy('menu_sort', 'asc')
            ->get();
        $menuType = MenuType::all();

        $data = [
            'listMenu' => $listMenu,
            'type' => $menuType
        ];

        return view('content.menu.index', $data);
    }

    public function changeRole(Request $request)
    {
        $roleId = $request->input('activeRole');

        $roleUser = $this->getCurrentUser()->data->roles;

        $roleData = collect($roleUser)->firstWhere('role_id', $roleId);

        session(['roles' => $roleData]);

        return redirect()->back()->with('success', 'Role berhasil diubah.');

    }

    public function menuAccess()
    {
        $selectedRoleId = null;
        $roles = Role::all();
        $rolePermissions = RolePermission::where('role_id', $selectedRoleId)->get()->keyBy('menu_id');

        $data = [
            'roles' => $roles,
            'roleAccess' => $rolePermissions
        ];

        return view('content.menu.access', $data);
    }

}
