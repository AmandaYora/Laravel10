<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/auth-login', [AuthController::class, 'login'])->name('auth.login');

Route::get('/users', [UserController::class, 'user'])->name('user.index');
Route::post('/users/save', [UserController::class, 'saveUser'])->name('users.save');

Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');