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

    public function saveMenu($data, $update = false, $menuId = null, $column = [])
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

            // Menggunakan generateContent untuk membuat konten berdasarkan $column
            $content = $this->generateContent($menu->menu, $column);
            File::put($indexFilePath, $content);

            // Nama model dan tabel sesuai dengan nama menu
            $modelName = ucfirst($menu->menu);
            $tableName = Str::snake($menu->menu) . 's';

            // Menggunakan generateModel untuk membuat model dinamis
            $this->generateModel($modelName, $tableName, $column);

            // Membuat controller menggunakan generateController
            $this->generateController($menu->menu, $column);

            // Menggunakan generateMigrations untuk membuat migration sesuai dengan kolom
            $this->generateMigrations($tableName, $column); // Tambahkan pemanggilan ini

            // Modifikasi routes/web.php untuk menambahkan rute baru
            $routePath = base_path('routes/web.php');
            $webFileContent = File::get($routePath);
            $hasRouteUseStatement = strpos($webFileContent, 'use Illuminate\Support\Facades\Route;') !== false;
            $useControllerStatement = "use App\Http\Controllers\\" . ucfirst($menu->menu) . "Controller;";

            // Tambahkan pernyataan `use` jika belum ada
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

            // Tambahkan rute index, save, dan delete untuk controller
            $routeStatements = [
                "Route::get('" . Str::slug($menu->menu_redirect) . "', [" . ucfirst($menu->menu) . "Controller::class, 'index'])->name('" . Str::slug($menu->menu) . ".index');",
                "Route::post('" . Str::slug($menu->menu_redirect) . "/save', [" . ucfirst($menu->menu) . "Controller::class, 'save" . ucfirst($menu->menu) . "'])->name('" . Str::slug($menu->menu) . ".save');",
                "Route::delete('" . Str::slug($menu->menu_redirect) . "/delete/{id}', [" . ucfirst($menu->menu) . "Controller::class, 'delete" . ucfirst($menu->menu) . "'])->name('" . Str::slug($menu->menu) . ".delete');"
            ];

            // Tambahkan rute baru ke `web.php` jika belum ada
            foreach ($routeStatements as $routeStatement) {
                if (strpos($webFileContent, $routeStatement) === false) {
                    $closingBracePosition = strrpos($webFileContent, '});');
                    if ($closingBracePosition !== false) {
                        $webFileContent = substr_replace($webFileContent, "\n" . $routeStatement . "\n", $closingBracePosition, 0);
                    }
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




    private function generateModel($modelName, $tableName, $columns = [])
    {
        $primaryKey = $columns[0] ?? 'id'; // Primary key adalah kolom pertama jika ada, default ke 'id'
        $fillable = array_slice($columns, 1); // Fillable adalah seluruh kolom setelah primary key

        // Template model
        $modelTemplate = <<<EOD
    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class $modelName extends Model
    {
        use HasFactory;

        protected \$table = '$tableName';

        protected \$primaryKey = '$primaryKey';

        protected \$fillable = [
    EOD;

        // Menambahkan kolom fillable secara dinamis
        foreach ($fillable as $column) {
            $modelTemplate .= "\n        '$column',";
        }

        $modelTemplate .= "\n    ];\n\n";
        $modelTemplate .= "    public \$timestamps = true;\n";

        // Menutup template kelas
        $modelTemplate .= "}\n";

        // Menentukan path model
        $modelPath = app_path("Models/$modelName.php");

        // Simpan file model jika belum ada
        if (!File::exists($modelPath)) {
            File::put($modelPath, $modelTemplate);
        }
    }


    private function generateContent($menuName, $columns = [])
    {
        $tableHeaders = '';
        $tableColumns = '';

        if (!empty($columns)) {
            foreach ($columns as $column) {
                $tableHeaders .= "<th>" . ucfirst($column) . "</th>\n";
                $tableColumns .= "<td>{{ \$item->$column }}</td>\n";
            }

            $tableHeaders .= "<th>Action</th>\n";

            $dataAttributes = '';
            foreach ($columns as $col) {
                $dataAttributes .= " data-{$col}=\"{{ \$item->{$col} }}\"";
            }

            $tableColumns .= <<<EOD
    <td>
        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editItemModal" 
                data-id="{{ \$item->{$columns[0]} }}" $dataAttributes>
            Edit
        </button>
        <form action="{{ route('$menuName.delete', \$item->{$columns[0]}) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </td>
    EOD;
        } else {
            return "<h1>Welcome to " . ucfirst($menuName) . " page</h1>\n";
        }

        $modalFields = '';
        foreach ($columns as $col) {
            $modalFields .= "<div class=\"mb-3\">
                                <label for=\"add" . ucfirst($col) . "\" class=\"form-label\">" . ucfirst($col) . "</label>
                                <input type=\"text\" class=\"form-control\" id=\"add" . ucfirst($col) . "\" name=\"$col\" required>
                            </div>\n";
        }

        $editModalFields = '';
        foreach ($columns as $col) {
            $editModalFields .= "<div class=\"mb-3\">
                                    <label for=\"edit" . ucfirst($col) . "\" class=\"form-label\">" . ucfirst($col) . "</label>
                                    <input type=\"text\" class=\"form-control\" id=\"edit" . ucfirst($col) . "\" name=\"$col\" required>
                                </div>\n";
        }

        // Menghasilkan string JavaScript secara eksplisit untuk setiap kolom
        $jsSetFields = '';
        foreach ($columns as $col) {
            $jsSetFields .= "$('#edit" . ucfirst($col) . "').val(button.data('$col'));\n";
        }

        return <<<EOD
    @extends('layouts.app')

    @section('title', ucfirst('$menuName') . ' Management')

    @section('content')
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">Data $menuName</h6>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                Tambah $menuName
                            </button>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
                                <strong>Error!</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                                <strong>Success!</strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="dataTableExample" class="table">
                                <thead>
                                    <tr>
                                        $tableHeaders
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (\$$menuName as \$item)
                                        <tr>
                                            $tableColumns
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Adding Item -->
        <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah $menuName</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('$menuName.save') }}">
                        @csrf
                        <div class="modal-body">
                            $modalFields
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Tambah $menuName</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal for Editing Item -->
        <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit $menuName</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('$menuName.update') }}">
                        @csrf
                        <input type="hidden" id="editItemId" name="id">
                        <div class="modal-body">
                            $editModalFields
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script>
            $('#editItemModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                $('#editItemId').val(id);

                $jsSetFields
            });

            $(document).ready(function() {
                setTimeout(function() {
                    $('#errorAlert').fadeOut('slow');
                }, 4000);

                setTimeout(function() {
                    $('#successAlert').fadeOut('slow');
                }, 4000);
            });
        </script>
    @endsection
    EOD;
    }

    private function generateController($menuName, $columns = [])
    {
        $controllerName = ucfirst($menuName) . 'Controller';
        $controllerPath = app_path("Http/Controllers/{$controllerName}.php");
        $primaryKey = $columns[0] ?? 'id';

        // Membuat template controller
        $controllerTemplate = <<<EOD
    <?php

    namespace App\Http\Controllers;

    use App\Models\\$menuName;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;

    class $controllerName extends Controller
    {
        // Method index untuk menampilkan halaman utama
        public function index()
        {
            \$$menuName = $menuName::all();
            return view('content.$menuName.index', compact('$menuName'));
        }

        // Method untuk menyimpan data baru atau update data
        public function save$menuName(Request \$request)
        {
            // Ambil hanya kolom yang ada dalam array \$columns
            \$data = \$request->only([
    EOD;

        // Tambahkan kolom ke dalam list pengambilan data dari request
        foreach ($columns as $column) {
            $controllerTemplate .= "\n            '$column',";
        }

        $controllerTemplate .= <<<EOD
            ]);

            \$itemId = \$request->input('$primaryKey');
            \$isUpdate = !empty(\$itemId);

            if (\$isUpdate) {
                \$item = $menuName::find(\$itemId);
                if (!\$item) {
                    return redirect()->back()->withErrors(['error' => 'Data not found.']);
                }
            } else {
                \$item = new $menuName();
            }

            \$item->fill(\$data);

            if (\$item->save()) {
                return redirect()->route('$menuName.index')->with('success', 'Data saved successfully.');
            } else {
                return redirect()->back()->withErrors(['error' => 'Failed to save data.']);
            }
        }

        // Method untuk menghapus data
        public function delete$menuName(\$id)
        {
            \$item = $menuName::find(\$id);
            if (\$item && \$item->delete()) {
                return redirect()->route('$menuName.index')->with('success', 'Data deleted successfully.');
            }
            return redirect()->back()->withErrors(['error' => 'Failed to delete data.']);
        }
    }
    EOD;

        // Simpan file controller jika belum ada
        if (!File::exists($controllerPath)) {
            File::put($controllerPath, $controllerTemplate);
        }
    }

    private function generateMigrations($tableName, $columns = [])
    {
        $primaryKey = $columns[0] ?? 'id'; // Primary key adalah kolom pertama
        $timestamp = date('Y_m_d_His'); // Digunakan untuk nama file migration unik berdasarkan waktu
        $migrationName = "create_{$tableName}_table";
        $fileName = "{$timestamp}_{$migrationName}.php";
        $migrationPath = database_path("migrations/{$fileName}");

        // Awal dari template migration
        $migrationTemplate = <<<EOD
    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class Create{$tableName}Table extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('$tableName', function (Blueprint \$table) {
                \$table->id('$primaryKey'); // Menggunakan primary key yang ditentukan
    EOD;

        // Tambahkan kolom selain primary key
        foreach (array_slice($columns, 1) as $column) {
            $migrationTemplate .= "\n            \$table->string('$column');";
        }

        // Tambahkan timestamps di akhir schema
        $migrationTemplate .= <<<EOD

                \$table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('$tableName');
        }
    }
    EOD;

        // Simpan file migration jika belum ada
        if (!File::exists($migrationPath)) {
            File::put($migrationPath, $migrationTemplate);
        }
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

        // Hapus rute yang terkait dari routes/web.php
        $routePath = base_path('routes/web.php');
        $webFileContent = File::get($routePath);

        // Hapus use statement untuk controller
        $controllerName = ucfirst($menu->menu) . 'Controller';
        $useControllerStatement = "use App\Http\Controllers\\" . $controllerName . ";";
        if (strpos($webFileContent, $useControllerStatement) !== false) {
            $webFileContent = str_replace($useControllerStatement . "\n", '', $webFileContent);
        }

        // Hapus rute-rute yang sesuai (index, save, delete)
        $routeStatements = [
            "Route::get('" . Str::slug($menu->menu_redirect) . "', [" . $controllerName . "::class, 'index'])->name('" . Str::slug($menu->menu) . ".index');",
            "Route::post('" . Str::slug($menu->menu_redirect) . "/save', [" . $controllerName . "::class, 'save" . ucfirst($menu->menu) . "'])->name('" . Str::slug($menu->menu) . ".save');",
            "Route::delete('" . Str::slug($menu->menu_redirect) . "/delete/{id}', [" . $controllerName . "::class, 'delete" . ucfirst($menu->menu) . "'])->name('" . Str::slug($menu->menu) . ".delete');"
        ];

        foreach ($routeStatements as $routeStatement) {
            if (strpos($webFileContent, $routeStatement) !== false) {
                $webFileContent = str_replace($routeStatement . "\n", '', $webFileContent);
            }
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
