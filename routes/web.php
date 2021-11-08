<?php

use App\Http\Controllers\BusinessConfigController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
  return Inertia::render('Welcome', [
    'canLogin' => Route::has('login'),
    'canRegister' => Route::has('register'),
    'laravelVersion' => Application::VERSION,
    'phpVersion' => PHP_VERSION,
  ]);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
  // DASBOARD
  Route::get('/panel', function () {
    return Inertia::render('Dashboard');
  })->name('dashboard');

  //USERS
  Route::resource('usuarios', UserController::class)->names([
    'index' => 'users.index',
    // 'create' => 'users.create',
    // 'store' => 'users.store',
    // 'show' => 'users.show',
    // 'edit' => 'users.edit',
    // 'update' => 'users.update',
    // 'destroy' => 'users.destroy'
  ])->parameters([
    'usuarios' => 'user'
  ])->only('index');

  //RUTA PARA CONFIGURAR SITIO
  // Route::resource('configuracion', BusinessConfigController::class)
  //   ->only('index', 'update')
  //   ->names([
  //     'index' => 'config.index',
  //     'update' => 'config.update',
  //   ])->parameters([
  //     'configuracion' => 'businessConfig'
  //   ]);
  Route::get('/configuracion', [BusinessConfigController::class, 'index'])->name('config.index');
  Route::put('/update-basic-config', [BusinessConfigController::class, 'updateBasicConfig'])->name('config.updateBasicConfig');
  Route::delete('/delete-logo', [BusinessConfigController::class, 'deleteLogo'])->name('config.deleteLogo');
  Route::delete('/delete-favicon', [BusinessConfigController::class, 'deleteFavicon'])->name('config.deleteFavicon');
});
