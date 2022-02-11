<?php

namespace App\Http\Controllers;

use App\Models\BusinessConfig;
use App\Models\CountryDepartment;
use App\Models\Town;
use App\Models\TownDistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use stdClass;

class BusinessConfigController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $departments = $this->getCountryDepartments();
    return Inertia::render('Config/Index', compact('departments'));
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

  public function storeTownDistrict(Request $request)
  {
    $rules = $this->getDistrictRules();
    $attr = $this->getDistrictAttr();
    $request->validate($rules, [], $attr);


    $inputs = $request->all();

    //Se crea la instancia del barrio
    /** @var TownDistrict */
    $district = new TownDistrict();
    $district->name = $inputs['name'];

    //Se recupera la instancia del pueblo
    /** @var Town */
    $town = Town::find($inputs['town_id'], ['id', 'name']);

    //Se guarda el nuevo barrio
    $town->districts()->save($district);
    $message = [
      'ok' => true,
      'town' => $town->name,
      'district' => $district->name
    ];

    return Redirect::route('config.index')->with('mesage', $message);
  }

  public function updateTownDistrict(Request $request)
  {
    $rules = $this->getDistrictRules();
    $attr = $this->getDistrictAttr();
    $request->validate($rules, [], $attr);
    $inputs = $request->all();

    $district = TownDistrict::find($inputs['district_id']);
    $district->name = $inputs['name'];
    $district->save();

    return Redirect::route('config.index');
  }

  public function destroyTownDistrict(Request $request)
  {
    $data = $request->all();
    $district = TownDistrict::find($data['id']);
    $res = new stdClass();

    if($district){
      $district->delete();
      $res->ok = true;
      $res->district = $district->toArray();
    }else{
      $res->ok = false;
      $res->district = $data;
    }

    return Redirect::route('config.index')->with('message', $res);
  }

  /**
   * Se encarga de consultar la base de datos
   * y recuperar la informacion de los departamentos, con sus ciudades 
   * y distritos
   * @return Collection
   */
  protected function getCountryDepartments()
  {
    return CountryDepartment::orderBy('name')->with([
      'towns' => function ($query) {
        $query->select(['id', 'country_department_id', 'name'])
          ->orderBY('name')
          ->with(['districts' => function ($query) {
            $query->select(['id', 'town_id', 'name'])
              ->orderBY('name');
          }]);
      }
    ])->get(['id', 'name']);
  }

  /**
   * Construye las reglas de validación para la creación
   * o la actualización de barrios en la base de datos.
   * @param bool $update Define si son reglas de actualización.
   */
  protected function getDistrictRules($update = false)
  {
    $rules = [
      'department_id' => 'required|integer|exists:country_department,id',
      'town_id' => 'required|integer|exists:town,id',
      'name' => 'required|string|min:3',
    ];

    if ($update) {
      $rules['district_id'] = 'required|integer|exists:town_distric,id';
    }

    return $rules;
  }

  /**
   * De
   */
  protected function getDistrictAttr()
  {
    return [
      'department_id' => 'departamento',
      'town_id' => 'municipio',
      'district_id' => 'barrio',
      'name' => 'nombre del barrio'
    ];
  }
}
