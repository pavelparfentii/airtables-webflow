<?php

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

Route::redirect('/', '/home');


Route::get( 'home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');

Route::resource('admins', App\Http\Controllers\AdminController::class)->middleware('auth');



//Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web']], function () {
//    \UniSharp\LaravelFilemanager\Lfm::routes();
//});

Route::get('login', [\App\Http\Controllers\AuthController::class, 'create'])->name('login');
Route::post('login', [\App\Http\Controllers\AuthController::class, 'store'])->name('login.store');
Route::delete('logout', [\App\Http\Controllers\AuthController::class, 'destroy'])->name('logout');


