<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\MenuController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/login', [ApiController::class, 'login'])->name('api.login');
Route::post('/register', [ApiController::class, 'register'])->name('api.register');
Route::post('/get-user', [ApiController::class, 'getUser'])->name('api.getUser');
Route::post('/get-user-attribute', [ApiController::class, 'getAttribute'])->name('api.getUserAttribute');
Route::post('/save-user-attribute', [ApiController::class, 'saveUserAttribute'])->name('api.saveUserAttribute');

Route::post('/menu/move-up', [MenuController::class, 'menuMoveUp'])->name('api.menu.up');
Route::post('/menu/move-down', [MenuController::class, 'menuMoveDown'])->name('api.menu.down');
