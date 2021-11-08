<?php

namespace App\Http\Controllers;

use App\Models\BusinessConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BusinessConfigController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return Inertia::render('Config/Index');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\BusinessConfig  $businessConfig
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, BusinessConfig $businessConfig)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\BusinessConfig  $businessConfig
   * @return \Illuminate\Http\Response
   */
  public function updateBasicConfig(Request $request)
  {
    $rules = [
      'name' => 'required|string|min:3|max:45',
      'logo' => 'nullable|image|max:1024',
      'favicon' => 'nullable|image|max:1024'
    ];

    $request->validate($rules, [], ['name' => 'nombre']);
    $inputs = $request->all();

    DB::beginTransaction();

    //Se recupera la configuración
    $businessConfig = BusinessConfig::first(['id', 'name', 'logo', 'favicon']);
    if ($businessConfig->name !== $request->name) {
      $businessConfig->name = $inputs['name'];
    }

    try {
      if ($request->hasFile('logo')) {
        //Se crea el nombre del archivo
        $logoName = uniqid('logo_') . '.' . $request->logo->extension();
        $path = $request->logo->storeAs('brand', $logoName, 'public');

        //Se elimina el antiguo logo
        if ($businessConfig->logo) {
          if (Storage::disk('public')->exists($businessConfig->logo)) {
            Storage::disk('public')->delete($businessConfig->logo);
          }
        }

        //Se guarda el nuevo logo
        $businessConfig->logo = $path;
      }

      if ($request->hasFile('favicon')) {
        //Se crea el nombre del archivo
        $name = uniqid('favicon_') . '.' . $request->favicon->extension();
        $path = $request->favicon->storeAs('brand', $name, 'public');

        //Se elimina el antiguo logo
        if ($businessConfig->favicon) {
          if (Storage::disk('public')->exists($businessConfig->favicon)) {
            Storage::disk('public')->delete($businessConfig->favicon);
          }
        }

        //Se guarda el nuevo logo
        $businessConfig->favicon = $path;
      }

      $businessConfig->save();
      DB::commit();
    } catch (\Throwable $th) {
      DB::rollBack();
      dd($th);
    }

    return Redirect::route('config.index');
  }

  public function deleteLogo()
  {
    $businessConfig = BusinessConfig::first(['id', 'logo']);
    if ($businessConfig && $businessConfig->logo) {
      if (Storage::disk('public')->exists($businessConfig->logo)) {
        Storage::disk('public')->delete($businessConfig->logo);
      }

      $businessConfig->logo = null;
      $businessConfig->save();
    }

    return Redirect::route('config.index');
  }

  public function deleteFavicon()
  {
    $businessConfig = BusinessConfig::first(['id', 'favicon']);
    if ($businessConfig && $businessConfig->favicon) {
      if (Storage::disk('public')->exists($businessConfig->favicon)) {
        Storage::disk('public')->delete($businessConfig->favicon);
      }

      $businessConfig->favicon = null;
      $businessConfig->save();
    }

    return Redirect::route('config.index');
  }
}
