<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\MenuType;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('submenus', 'menuType')->get();
        $menuType = MenuType::all();

        $data = [
            'menus' => $menus,
            'type' => $menuType
        ];

        return view('content.menu.index', $data);
    }
}
