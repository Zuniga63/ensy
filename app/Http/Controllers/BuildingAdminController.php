<?php

namespace App\Http\Controllers;

use App\Models\BuildingAdmin;
use App\Models\CountryDepartment;
use App\Models\TownDistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class BuildingAdminController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    /**
     * @var BuildingAdmin
     */
    $admins = BuildingAdmin::orderBy('name')
      ->withCount('buildings')
      ->withSum('buildings as fees', 'admin_fee')
      ->get();

    return Inertia::render('BuildingAdmin/Index', compact('admins'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $departments = $this->getCountryDepartments();
    $allDistricts = TownDistrict::orderBy('name')->with('town')->get();
    return Inertia::render('BuildingAdmin/Create', compact('departments', 'allDistricts'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $rules = $this->getRules();
    $attr = $this->getAttr();
    $request->validate($rules, [], $attr);
    $inputs = $request->all();

    $admin = BuildingAdmin::create($inputs);

    $result = [
      'ok' => true,
      'admin' => $admin
    ];

    if ($inputs['addOtherAdmin']) {
      $result['reload'] = true;
      return Redirect::route('buildingAdmin.create')->with('message', $result);
    } else {
      return Redirect::route('buildingAdmin.index')->with('message', $result);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\BuildingAdmin  $buildingAdmin
   * @return \Illuminate\Http\Response
   */
  public function show(BuildingAdmin $buildingAdmin)
  {
    $buildingAdmin->load('buildings');

    return Inertia::render('BuildingAdmin/Show', compact('buildingAdmin'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\BuildingAdmin  $buildingAdmin
   * @return \Illuminate\Http\Response
   */
  public function edit(BuildingAdmin $buildingAdmin)
  {
    $departments = $this->getCountryDepartments();
    $allDistricts = TownDistrict::orderBy('name')->with('town')->get();
    return Inertia::render('BuildingAdmin/Edit', compact('buildingAdmin', 'departments', 'allDistricts'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\BuildingAdmin  $buildingAdmin
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, BuildingAdmin $buildingAdmin)
  {
    $rules = $this->getRules();
    $attr = $this->getAttr();
    $request->validate($rules, [], $attr);

    $buildingAdmin->name = $request->input('name');
    $buildingAdmin->country_department_id = $request->input('country_department_id');
    $buildingAdmin->town_id = $request->input('town_id');
    $buildingAdmin->town_district_id = $request->input('town_district_id');
    $buildingAdmin->address = $request->input('address');
    $buildingAdmin->admin_first_name = $request->input('admin_first_name');
    $buildingAdmin->admin_last_name = $request->input('admin_last_name');
    $buildingAdmin->admin_document_number = $request->input('admin_document_number');
    $buildingAdmin->phones = $request->input('phones');
    $buildingAdmin->email = $request->input('email');
    $buildingAdmin->save();

    $result = [
      'ok' => true,
      'admin' => $buildingAdmin
    ];

    return Redirect::route('buildingAdmin.index')->with('message', $result);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\BuildingAdmin  $buildingAdmin
   * @return \Illuminate\Http\Response
   */
  public function destroy(BuildingAdmin $buildingAdmin)
  {
    $buildingAdmin->delete();
    $result = [
      'ok' => true,
      'admin' => $buildingAdmin
    ];

    return $result;
  }

  //-------------------------------------------------------------------------------
  //-------------------------------------------------------------------------------
  //  UTILIDADES
  //-------------------------------------------------------------------------------
  //-------------------------------------------------------------------------------

  /**
   * Recupera de la base de datos los departamentos y
   * municipios que tienen asignados barrios.
   * @return Collection
   */
  protected function getCountryDepartments()
  {
    return CountryDepartment::orderBy('name')
      ->has('towns.districts')->with([
        'towns' => function ($query) {
          $query->select(['id', 'country_department_id', 'name'])
            ->orderBY('name')
            ->has('districts')
            ->with(['districts' => function ($query) {
              $query->select(['id', 'town_id', 'name'])
                ->with('town')
                ->orderBY('name');
            }]);
        }
      ])->get(['id', 'name']);
  }

  /**
   * Define las reglas de validación tanto para crear 
   * un nuevo grupo de administración como para editarlo.
   */
  protected function getRules()
  {
    return [
      'name' => 'required|string|min:3|max:45',
      'country_department_id' => 'nullable|integer|exists:country_department,id',
      'town_id' => 'nullable|integer|exists:town,id',
      'town_district_id' => 'nullable|integer|exists:town_district,id',
      'address' => 'nullable|string|min:3|max:255',
      'admin_first_name' => 'nullable|string|min:3|max:45',
      'admin_last_name' => 'nullable|string|min:3|max:45',
      'admin_document_number' => 'nullable|string|min:6|max:20',
      'phones' => 'nullable|array',
      'email' => 'nullable|string|email:rfc,dns'
    ];
  }

  protected function getAttr()
  {
    return [
      'name' => 'nombre',
      'country_department_id' => 'departamento',
      'town_id' => 'municipio',
      'town_district_id' => 'barrio',
      'address' => 'dirección',
      'admin_first_name' => 'nombres del administrador',
      'admin_last_name' => 'apellidos del adminsitrador',
      'admin_document_number' => 'numero de documento',
      'phones' => 'telefonos',
      'email' => 'correo'
    ];
  }
}
