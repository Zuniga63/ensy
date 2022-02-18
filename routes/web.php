<?php

use App\Http\Controllers\BusinessConfigController;
use App\Http\Controllers\Cashbox\CashboxController;
use App\Http\Controllers\CustomerController;
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

// Route::get('/', function () {
//   return Inertia::render('Welcome', [
//     'canLogin' => Route::has('login'),
//     'canRegister' => Route::has('register'),
//     'laravelVersion' => Application::VERSION,
//     'phpVersion' => PHP_VERSION,
//   ]);
// });

Route::redirect('/', '/panel', 301);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
  // DASBOARD
  Route::get('/panel', function () {
    return Inertia::render('Dashboard');
  })->name('dashboard');

  //USERS
  Route::resource('usuarios', UserController::class)->names([
    'index' => 'users.index',
    'store' => 'users.store',
    'destroy' => 'users.destroy',
  ])->parameters([
    'usuarios' => 'user'
  ])->only(['index', 'store', 'destroy']);

  //CASHBOX
  Route::resource('cajas', CashboxController::class)->names([
    'index' => 'cashbox.index',
    'create' => 'cashbox.create',
    'store' => 'cashbox.store',
    'update' => 'cashbox.update',
    'destroy' => 'cashbox.destroy',
  ])->parameters([
    'cajas' => 'cashbox',
  ])->except(['show', 'edit']);

  Route::resource('cajas', CashboxController::class)->names([
    'show' => 'cashbox.show',
    'edit' => 'cashbox.edit',
  ])->parameters([
    'cajas' => 'cashbox:slug'
  ])->only(['show', 'edit']);

  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  //Rutas para menejar transacciones
  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  Route::post('/cajas/{cashbox}/registrar-transaccion', [CashboxController::class, 'storeTransaction'])->name('cashbox.storeTransaction');
  Route::put('/cajas/{cashbox}/{cashbox_transaction}', [CashboxController::class, 'updateTransaction'])->name('cashbox.updateTransaction');
  Route::delete('/cajas/{cashbox}/{cashbox_transaction}', [CashboxController::class, 'destroyTransaction'])->name('cashbox.destroyTransaction');
  //Rutas para manejar transferencia
  Route::post('/cajas/{cashbox}/registrar-transferencia', [CashboxController::class, 'storeTransfer'])->name('cashbox.storeTransfer');

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

  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  //RUTAS PARA LA ADMINISTRACIÓN DE BARRIOS
  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  Route::post('/configuracion/new-town-district', [BusinessConfigController::class, 'storeTownDistrict'])->name('config.storeTownDistrict');
  Route::put('/configuracion/update-town-district', [BusinessConfigController::class, 'updateTownDistrict'])
    ->name('config.updateTownDistrict');
  Route::delete('/configuracion/destroy-town-district', [BusinessConfigController::class, 'destroyTownDistrict'])
    ->name('config.destroyTownDistrict');

  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  // RUTAS PARA ADMINSITRAR CLIENTES
  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  Route::resource('clientes', CustomerController::class)
    ->names([
      'index' => 'customer.index',
      'create' => 'customer.create',
      'store' => 'customer.store',
      'edit' => 'customer.edit',
      'update' => 'customer.update',
      'destroy' => 'customer.destroy',
    ])
    ->parameters([
      'clientes' => 'customer'
    ]);
});
