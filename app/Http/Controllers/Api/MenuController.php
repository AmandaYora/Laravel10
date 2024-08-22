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

        if (!isset($data['menu_id'])) {
            $result->code = Result::CODE_ERROR;
            $result->info = "menu_id is required!";
            return $this->responseApi($result);
        }

        $menuModel = new Menus();
        $menuUp = $menuModel->moveUp($data['menu_id']);

        if (!$menuUp) {
            $success = false;
            $result->code = Result::CODE_ERROR;
            $result->info = "failed to move up";
        }

        if($success){
            $menus = Menu::with('submenus', 'menuType')->get();
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

        if (!isset($data['menu_id'])) {
            $result->code = Result::CODE_ERROR;
            $result->info = "menu_id is required!";
            return $this->responseApi($result);
        }

        $menuModel = new Menus();
        $menuUp = $menuModel->moveDown($data['menu_id']);

        if (!$menuUp) {
            $success = false;
            $result->code = Result::CODE_ERROR;
            $result->info = "failed to move down";
        }

        if($success){
            $menus = Menu::with('submenus', 'menuType')->get();
            $result->data = $menus;
        }

        return $this->responseApi($result);
    }
}
