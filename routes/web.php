<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/roles', AdminController::class . '@roles')->name('roles');
    Route::get('/permissions', AdminController::class . '@permissions')->name('permissions');
    Route::get('/user_roles', AdminController::class . '@userRoles')->name('user_roles');
});

require __DIR__ . '/auth.php';
