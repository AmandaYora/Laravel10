<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Result;
use App\Services\Entities;
use App\Services\Person;
use App\Services\Menus;
use App\Models\Menu;
use App\Services\Notification;
use Illuminate\Support\Facades\Session;

class MenuController extends Controller
{
    public function menuMoveUp()
    {
        Entities::Publish();

        $data = $this->getRequest->data;
        $result = new Result();
        $success = true;

        $isMenu = isset($data['is_menu']) ? $data['is_menu'] : true;
        
        if ($isMenu) {
            $menu = $data['menu_id'];
            $mode = Menus::MODE_MENU;
        }else {
            $menu = $data['submenu_id'];
            $mode = Menus::MODE_SUBMENU;
        }

        if (!isset($menu)) {
            $result->code = Result::CODE_ERROR;
            $result->info = "menu_id or submenu_id is required!";
            return $this->responseApi($result);
        }

        $menuModel = new Menus();
        $menuUp = $menuModel->moveUp($menu, $mode);

        if (!$menuUp) {
            $success = false;
            $result->code = Result::CODE_ERROR;
            $result->info = "failed to move up";
        }

        if($success){
            $menus = Menu::with('submenus', 'menuType')
            ->orderBy('menu_sort', 'asc')
            ->get();

            $result->data = $menus;
        }

        return $this->responseApi($result);
    }

    public function menuMoveDown()
    {
        Entities::Publish();

        $data = $this->getRequest->data;
        $result = new Result();
        $success = true;

        $isMenu = isset($data['is_menu']) ? $data['is_menu'] : true;
        if ($isMenu) {
            $menu = $data['menu_id'];
            $mode = Menus::MODE_MENU;
        }else {
            $menu = $data['submenu_id'];
            $mode = Menus::MODE_SUBMENU;
        }

        if (!isset($menu)) {
            $result->code = Result::CODE_ERROR;
            $result->info = "menu_id is required!";
            return $this->responseApi($result);
        }

        $menuModel = new Menus();
        $menuUp = $menuModel->moveDown($menu, $mode);

        if (!$menuUp) {
            $success = false;
            $result->code = Result::CODE_ERROR;
            $result->info = "failed to move down";
        }

        if($success){
            $menus = Menu::with('submenus', 'menuType')
            ->orderBy('menu_sort', 'asc')
            ->get();

            $result->data = $menus;
        }

        return $this->responseApi($result);
    }

    public function menuSave()
    {
        Entities::Publish();

        $data = $this->getRequest->data;
        $result = new Result();
        
        $update = false;
        $menuId = null;

        if (isset($data['menu_id'])) {
            $update = true;
            $menuId = $data['menu_id'];
        }

        $menuModel = new Menus();
        $save = $menuModel->saveMenu($data, $update, $menuId);

        if ($save){
            $result->data =  $menus = Menu::with('submenus', 'menuType')
            ->orderBy('menu_sort', 'asc')
            ->get();
        } else {
            $result->code = Result::CODE_ERROR;
            $result->info = "Failed save menu";
        }

        return $this->responseApi($result);
    }

    public function menuDelete()
    {
        Entities::Publish();

        $data = $this->getRequest->data;
        $result = new Result();

        $menuModel = new Menus();
        $save = $menuModel->menuDelete($data['menu_id']);

        if ($save){
            $result->data = Menu::with('submenus', 'menuType')
            ->orderBy('menu_sort', 'asc')
            ->get();
        } else {
            $result->code = Result::CODE_ERROR;
            $result->info = "Failed Delete menu";
        }

        return $this->responseApi($result);
    }
}
