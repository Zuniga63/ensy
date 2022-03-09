<?php

namespace App\Http\Controllers;

use App\Models\BuildingAdmin;
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
    $admins = BuildingAdmin::withCount('buildings')->orderBy('name')->get();
    return Inertia::render('BuildingAdmin/Index', compact('admins'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return Inertia::render('BuildingAdmin/Create');
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
    return Inertia::render('BuildingAdmin/Edit', compact('buildingAdmin'));
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
    $inputs = $request->all();

    $buildingAdmin->name = $inputs['name'];
    $buildingAdmin->address = $inputs['address'];
    $buildingAdmin->admin_first_name = $inputs['admin_first_name'];
    $buildingAdmin->admin_last_name = $inputs['admin_last_name'];
    $buildingAdmin->admin_document_number = $inputs['admin_document_number'];
    $buildingAdmin->phones = $inputs['phones'];
    $buildingAdmin->email = $inputs['email'];
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

  /**
   * Define las reglas de validación tanto para crear 
   * un nuevo grupo de administración como para editarlo.
   */
  protected function getRules()
  {
    return [
      'name' => 'required|string|min:3|max:45',
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
      'address' => 'dirección',
      'admin_first_name' => 'nombres del administrador',
      'admin_last_name' => 'apellidos del adminsitrador',
      'admin_document_number' => 'numero de documento',
      'phones' => 'telefonos',
      'email' => 'correo'
    ];
  }
}
