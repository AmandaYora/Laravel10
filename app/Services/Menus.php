<?php

namespace App\Services;

use App\Models\Menu as MenuModel;
use App\Models\Submenu as SubmenuModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

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

    public function saveMenu($data, $update = false, $menuId = null)
    {
        if ($update && $menuId) {
            $menu = MenuModel::find($menuId);
            if (!$menu) {
                return null;
            }
        } else {
            $menu = new MenuModel();
            $maxSortMenu = MenuModel::orderBy('menu_sort', 'desc')->first();
            $menu->menu_sort = $maxSortMenu ? $maxSortMenu->menu_sort + 1 : 1;
        }
    
        $menu->menu_icon = $data['menu_icon'];
        $menu->menu = $data['menu'];
        $menu->menu_slug = Str::slug($data['menu']);
        $menu->menu_type_id = $data['menu_type_id'];
        $menu->menu_redirect = $data['menu_redirect'];
    
        if (substr($menu->menu_redirect, 0, 1) !== '/') {
            $menu->menu_redirect = '/' . $menu->menu_redirect;
        }
    
        $menusave = $menu->save();
    
        if ($menusave) {
            $viewDirectoryPath = resource_path('views/content/' . $menu->menu_slug);
    
            if (!File::exists($viewDirectoryPath)) {
                File::makeDirectory($viewDirectoryPath, 0755, true);
            }
    
            $indexFilePath = $viewDirectoryPath . '/index.blade.php';
            $content = "<h1>Welcome to " . $menu->menu . " page</h1>\n";
    
            if (!File::exists($indexFilePath)) {
                File::put($indexFilePath, $content);
            }
    
            $controllerName = ucfirst($menu->menu) . 'Controller';
            $modelName = ucfirst($menu->menu);
    
            // Membuat file Model
            $modelPath = app_path('Models/' . $modelName . '.php');
            $modelTemplate = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass " . $modelName . " extends Model\n{\n    protected \$table = '" . Str::snake($menu->menu) . "s';\n    protected \$fillable = ['name'];\n}\n";
            if (!File::exists($modelPath)) {
                File::put($modelPath, $modelTemplate);
            }
    
            // Membuat file Controller
            $controllerPath = app_path('Http/Controllers/' . $controllerName . '.php');
            $controllerTemplate = "<?php\n\nnamespace App\Http\Controllers;\n\nuse App\Http\Controllers\Controller;\nuse Illuminate\Http\Request;\n\nclass " . $controllerName . " extends Controller\n{\n    public function index()\n    {\n        return view('content." . $menu->menu_slug . ".index');\n    }\n}\n";
            if (!File::exists($controllerPath)) {
                File::put($controllerPath, $controllerTemplate);
            }
    
            // Memodifikasi routes/web.php
            $routePath = base_path('routes/web.php');
            $webFileContent = File::get($routePath);
            $hasRouteUseStatement = strpos($webFileContent, 'use Illuminate\Support\Facades\Route;') !== false;
            $useControllerStatement = "use App\Http\Controllers\\" . $controllerName . ";";
    
            if ($hasRouteUseStatement) {
                if (strpos($webFileContent, $useControllerStatement) === false) {
                    $positionOfRoute = strpos($webFileContent, 'use Illuminate\Support\Facades\Route;');
                    $nextLinePosition = strpos($webFileContent, "\n", $positionOfRoute);
    
                    if (substr($webFileContent, $nextLinePosition + 1, 1) !== "\n") {
                        $webFileContent = substr_replace($webFileContent, "\n", $nextLinePosition + 1, 0);
                    }
    
                    $webFileContent = substr_replace($webFileContent, $useControllerStatement . "\n", $nextLinePosition + 1, 0);
                }
            }
    
            $closingBracePosition = strrpos($webFileContent, '});');
            $routeStatement = "Route::get('" . Str::slug($menu->menu_redirect) . "', [" . $controllerName . "::class, 'index'])->name('" . Str::slug($menu->menu) . ".index');";
    
            if ($closingBracePosition !== false) {
                if (strpos($webFileContent, $routeStatement) === false) {
                    $webFileContent = substr_replace($webFileContent, "\n" . $routeStatement . "\n", $closingBracePosition, 0);
                }
            }
    
            File::put($routePath, $webFileContent);
        }
        
        if ($update) {
            SubmenuModel::where('menu_id', $menu->menu_id)->delete();
        }
    
        if (isset($data['submenus']) && is_array($data['submenus']) && count($data['submenus']) > 0) {
            foreach ($data['submenus'] as $submenuData) {
                $submenu = new SubmenuModel();
                $submenu->menu_id = $menu->menu_id;
                $submenu->submenu = $submenuData['submenu'];
                $submenu->submenu_slug = Str::slug($submenuData['submenu']);
                $submenu->submenu_redirect = $submenuData['submenu_redirect'];
    
                if (substr($submenu->submenu_redirect, 0, 1) !== '/') {
                    $submenu->submenu_redirect = '/' . $submenu->submenu_redirect;
                }
    
                $maxSubSort = SubmenuModel::orderBy('submenu_sort', 'desc')->first();
                $submenu->submenu_sort = $maxSubSort ? $maxSubSort->submenu_sort + 1 : 1;
    
                $submenu->save();
            }
        }
    
        return $menu;
    }

    public function menuDelete($menuId = null)
    {
        if (!$menuId) {
            return false;
        }

        $menu = MenuModel::find($menuId);

        if (!$menu) {
            return false;
        }

        $deletedMenuSort = $menu->menu_sort;

        // Hapus submenus terkait
        SubmenuModel::where('menu_id', $menu->menu_id)->delete();

        // Hapus view directory terkait
        $viewDirectoryPath = resource_path('views/content/' . $menu->menu_slug);
        if (File::exists($viewDirectoryPath)) {
            File::deleteDirectory($viewDirectoryPath);
        }

        // Hapus file model yang terkait
        $modelPath = app_path('Models/' . ucfirst($menu->menu) . '.php');
        if (File::exists($modelPath)) {
            File::delete($modelPath);
        }

        // Hapus file controller yang terkait
        $controllerPath = app_path('Http/Controllers/' . ucfirst($menu->menu) . 'Controller.php');
        if (File::exists($controllerPath)) {
            File::delete($controllerPath);
        }

        // Hapus route yang terkait dari routes/web.php
        $routePath = base_path('routes/web.php');
        $webFileContent = File::get($routePath);

        // Hapus use statement untuk controller
        $controllerName = ucfirst($menu->menu) . 'Controller';
        $useControllerStatement = "use App\Http\Controllers\\" . $controllerName . ";";
        if (strpos($webFileContent, $useControllerStatement) !== false) {
            $webFileContent = str_replace($useControllerStatement . "\n", '', $webFileContent);
        }

        // Hapus route yang sesuai
        $routeStatement = "Route::get('" . Str::slug($menu->menu_redirect) . "', [" . $controllerName . "::class, 'index'])->name('" . Str::slug($menu->menu) . ".index');";
        if (strpos($webFileContent, $routeStatement) !== false) {
            $webFileContent = str_replace($routeStatement . "\n", '', $webFileContent);
        }

        // Simpan perubahan ke routes/web.php
        File::put($routePath, $webFileContent);

        // Hapus menu dari database
        $menu->delete();

        // Update menu_sort untuk menu lain yang ada di atas
        MenuModel::where('menu_sort', '>', $deletedMenuSort)->decrement('menu_sort');

        return true;
    }

    
}
