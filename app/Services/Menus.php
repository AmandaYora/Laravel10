<?php

namespace App\Services;

use App\Models\Menu as MenuModel;
use App\Models\Submenu as SubmenuModel;

class Menus
{
    const MODE_MENU = 1;
    const MODE_SUBMENU = 2;

    public function moveUp($menuId = null, $mode = self::MODE_MENU)
    {
        if (!$menuId) {
            return false;
        }

        if ($mode == self::MODE_MENU) {
            $menu = MenuModel::find($menuId);

            if (!$menu) {
                return false;
            }

            $currentSort = $menu->menu_sort;

            if ($currentSort <= 1) {
                return false;
            }

            $menuToSwap = MenuModel::where('menu_sort', $currentSort - 1)
                ->where('menu_type_id', $menu->menu_type_id)
                ->first();

            if ($menuToSwap) {
                $menuToSwap->menu_sort = $currentSort;
                $menuToSwap->save();

                $menu->menu_sort = $currentSort - 1;
                $menu->save();
            }

        } elseif ($mode == self::MODE_SUBMENU) {
            $submenu = SubmenuModel::find($menuId);

            if (!$submenu) {
                return false;
            }

            $currentSort = $submenu->submenu_sort;

            if ($currentSort <= 1) {
                return false;
            }

            $submenuToSwap = SubmenuModel::where('submenu_sort', $currentSort - 1)
                ->where('menu_id', $submenu->menu_id)
                ->first();

            if ($submenuToSwap) {
                $submenuToSwap->submenu_sort = $currentSort;
                $submenuToSwap->save();

                $submenu->submenu_sort = $currentSort - 1;
                $submenu->save();
            }
        }

        return true;
    }

    public function moveDown($menuId = null, $mode = self::MODE_MENU)
    {
        if (!$menuId) {
            return false;
        }

        if ($mode == self::MODE_MENU) {
            $menu = MenuModel::find($menuId);

            if (!$menu) {
                return false;
            }

            $currentSort = $menu->menu_sort;

            $menuToSwap = MenuModel::where('menu_sort', $currentSort + 1)
                ->where('menu_type_id', $menu->menu_type_id)
                ->first();

            if ($menuToSwap) {
                $menuToSwap->menu_sort = $currentSort;
                $menuToSwap->save();

                $menu->menu_sort = $currentSort + 1;
                $menu->save();
            }

        } elseif ($mode == self::MODE_SUBMENU) {
            $submenu = SubmenuModel::find($menuId);

            if (!$submenu) {
                return false;
            }

            $currentSort = $submenu->submenu_sort;

            $submenuToSwap = SubmenuModel::where('submenu_sort', $currentSort + 1)
                ->where('menu_id', $submenu->menu_id)
                ->first();

            if ($submenuToSwap) {
                $submenuToSwap->submenu_sort = $currentSort;
                $submenuToSwap->save();

                $submenu->submenu_sort = $currentSort + 1;
                $submenu->save();
            }
        }

        return true;
    }
}
