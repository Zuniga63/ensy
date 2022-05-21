<?php

use App\Http\Controllers\BuildingAdminController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\BusinessConfigController;
use App\Http\Controllers\CashboxController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DailyActivityController;
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

Route::redirect('/', '/panel', 302);

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

  Route::get('/configuracion', [BusinessConfigController::class, 'index'])->name('config.index');
  Route::put('/update-basic-config', [BusinessConfigController::class, 'updateBasicConfig'])->name('config.updateBasicConfig');
  Route::put('/update-socials-and-contacts', [BusinessConfigController::class, 'updateSocialsAndContacts'])->name('config.updateSocialsAndContacts');
  Route::delete('/delete-logo', [BusinessConfigController::class, 'deleteLogo'])->name('config.deleteLogo');
  Route::delete('/delete-favicon', [BusinessConfigController::class, 'deleteFavicon'])->name('config.deleteFavicon');
  Route::put('/update-commercial-information', [BusinessConfigController::class, 'updateCommercialInformation'])->name('config.updateCommercialInformation');

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
      'show' => 'customer.show',
      'edit' => 'customer.edit',
      'update' => 'customer.update',
      'destroy' => 'customer.destroy',
    ])
    ->parameters([
      'clientes' => 'customer'
    ]);

  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  // RUTAS PARA LOS ADMINSITRADORES DE INMUEBLES
  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------

  Route::resource('administraciones', BuildingAdminController::class)
    ->names([
      'index' => 'buildingAdmin.index',
      'create' => 'buildingAdmin.create',
      'store' => 'buildingAdmin.store',
      'edit' => 'buildingAdmin.edit',
      'update' => 'buildingAdmin.update',
      'destroy' => 'buildingAdmin.destroy',
    ])
    ->parameters([
      'administraciones' => 'buildingAdmin'
    ]);

  Route::put('/clientes/{customer}/update-customer-bank-information', [CustomerController::class, 'updateCustomerBankInformation'])
    ->name('customer.updateBankInformation');
  Route::post('/clientes/{customer}/store-customer-contact', [CustomerController::class, 'storeCustomerContact'])
    ->name('customer.sotreContact');
  Route::post('/clientes/{customer}/store-payment', [CustomerController::class, 'storePayment'])->name('customer.storePayment');
  Route::delete('/clientes/{customer}/{customer_contact}', [CustomerController::class, 'destroyCustomerContact'])
    ->name('customer.destroyContact');

  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  // RUTAS PARA LOS INMUEBLES
  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  Route::resource('inmuebles', BuildingController::class)
    ->names([
      'index' => 'building.index',
      'create' => 'building.create',
      'store' => 'building.store',
      'edit' => 'building.edit',
      'update' => 'building.update',
      'destroy' => 'building.destroy',
    ])
    ->parameters([
      'inmuebles' => 'building'
    ]);
  Route::put('/inmuebles/{building}/update-address', [BuildingController::class, 'updateAddress'])
    ->name('building.updateAddress');
  Route::put('/inmuebles/{building}/update-building-state', [BuildingController::class, 'updateBuildingState'])
    ->name('building.updateBuildingState');

  //-----------------------------------------------------------------------------
  //  ROUTES FOR INVOICE ADMIN
  //-----------------------------------------------------------------------------

  Route::resource('facturacion', InvoiceController::class)
    ->names([
      'index' => 'invoice.index',
      'store' => 'invoice.store',
      'show' => 'invoice.show'
    ])
    ->parameters(['facturacion' => 'invoice']);
  Route::put('facturar-pago', [InvoiceController::class, 'storePayments'])->name('invoice.storePayments');
  Route::put('cancelar-pago-de-factura', [InvoiceController::class, 'cancelPayment'])->name('invoice.cancelPayment');
  Route::put('cancelar-item-de-factura', [InvoiceController::class, 'cancelItem'])->name('invoice.cancelItem');
  Route::put('cancelar-factura', [InvoiceController::class, 'cancelInvoice'])->name('invoice.cancel');
  Route::get('reporte-facturacion/semanal', [InvoiceController::class, 'getWeeklyReport'])->name('invoice.weeklyReport');
  Route::get('imprimir-factura/{invoice}', [InvoiceController::class, 'printInvoice'])->name('invoice.print');

  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  // RUTAS PARA ADMINSITRAR ACTIVIDADES DIARIAS
  //-----------------------------------------------------------------------------
  //-----------------------------------------------------------------------------
  Route::get('actividades-diarias', [DailyActivityController::class, 'index'])->name('dailyActivity.index');
  Route::post('actividades-diarias', [DailyActivityController::class, 'store'])->name('dailyActivity.store');
  Route::delete('actividades-diarias/{dailyActivity}',  [DailyActivityController::class, 'destroy'])->name('dailyActivity.destroy');
});
