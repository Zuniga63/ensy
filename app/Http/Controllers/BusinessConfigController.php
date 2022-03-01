<?php

namespace App\Http\Controllers;

use App\Models\BusinessConfig;
use App\Models\CountryDepartment;
use App\Models\Town;
use App\Models\TownDistrict;
use Carbon\Carbon;
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
    $config = BusinessConfig::first();
    return Inertia::render('Config/Index', compact('departments', 'config'));
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

  /**
   * Se encarga de actualizar la información de contacto
   * y los enlaces a las rede sociales.
   * @return \Illuminate\Http\Response
   */
  public function updateSocialsAndContacts(Request $request)
  {
    $rules = [
      'phone' => 'nullable|string|min:6|max:20',
      'show_phone' => 'nullable|boolean',
      'address' => 'nullable|string|max:255',
      'show_address' => 'boolean',
      'email' => 'nullable|string|email:rfc,dns|max:255',
      'show_email' => 'nullable|boolean',
      'whatsapp' => 'nullable|string|min:6|max:20',
      'show_whatsapp' => 'boolean',
      'facebook_nick' => 'nullable|string|min:3|max:255',
      'facebook_link' => 'nullable|string|url|max:2048',
      'show_facebook' => 'boolean',
      'instagram_nick' => 'nullable|string|min:3|max:255',
      'instagram_link' => 'nullable|string|url|max:2048',
      'show_instagram' => 'boolean',
    ];

    $attr = [
      'phone' => 'telefono',
      'address' => 'dirección',
      'email' => 'correo electronico',
      'whatsapp' => 'WhatsApp',
      'facebook_nick' => 'facebook nick',
      'facebook_link' => 'facebook link',
      'instagram_nick' => 'instagram nick',
      'instagram_link' => 'instagram link',
    ];

    $request->validate($rules, [], $attr);
    $inputs = $request->all();

    //Recupero la configuración
    $config = BusinessConfig::first();

    $config->phone = $inputs['phone'];
    $config->show_phone = $config->phone && $inputs['show_phone'];

    $config->address = $inputs['address'];
    $config->show_address = $config->address && $inputs['show_address'];

    $config->email = $inputs['email'];
    $config->show_email = $config->email && $inputs['show_email'];

    $config->whatsapp = $inputs['whatsapp'];
    $config->show_whatsapp = $config->whatsapp && $inputs['show_whatsapp'];

    $config->facebook_nick = $inputs['facebook_nick'];
    $config->facebook_link = $inputs['facebook_link'];
    $config->show_facebook = $config->facebook_nick && $config->facebook_link && $inputs['show_facebook'];

    $config->instagram_nick = $inputs['instagram_nick'];
    $config->instagram_link = $inputs['instagram_link'];
    $config->show_instagram = $config->instagram_nick && $config->instagram_link && $inputs['show_instagram'];

    $config->save();

    return Redirect::route('config.index');
  }

  public function updateCommercialInformation(Request $request)
  {
    $rules = [
      'legal_representative_first_name' => 'nullable|string|min:3|max:45',
      'legal_representative_last_name' => 'nullable|string|min:3|max:45',
      'legal_representative_document' => 'nullable|string|max:20',
      'legal_representative_document_type' => 'required|in:CC,CE,PAP',
      'legal_representative_sex' => 'nullable|string|in:f,m',
      'legal_representative_tel' => 'nullable|string|min:6|max:20',
      'legal_representative_email' => 'nullable|string|email:rfc,dns|max:255',
      'nit' => 'nullable|string|max:45',
      'nit_name' => 'nullable|string|max:150',
      'nit_date_of_renovation' => 'nullable|string|date',
      'bank_name' => 'nullable|string|min:3|max:45',
      'bank_account_type' => 'required|string|in:savings,current',
      'bank_account_number' => 'nullable|string|max:255',
      'bank_account_holder' => 'nullable|string|min:3|max:255',
      'bank_account_holder_document' => 'nullable|string|max:20',
    ];

    $attr = [
      'legal_representative_first_name' => 'nombres',
      'legal_representative_last_name' => 'apellidos',
      'legal_representative_document' => 'documento',
      'legal_representative_document_type' => 'tipo',
      'legal_representative_sex' => 'sexo',
      'legal_representative_tel' => 'telefono',
      'legal_representative_email' => 'email',
      'nit' => 'nit',
      'nit_name' => 'nombre',
      'nit_date_of_renovation' => 'fecha de renovación',
      'bank_name' => 'bano',
      'bank_account_type' => 'tipo de cuenta',
      'bank_account_number' => 'numero de cuenta',
      'bank_account_holder' => 'titular',
      'bank_account_holder_document' => 'documento del titular',
    ];

    $request->validate($rules, [], $attr);
    $input = $request->all();
    $config = BusinessConfig::first();

    $config->legal_representative_first_name = $input['legal_representative_first_name'];
    $config->legal_representative_last_name = $input['legal_representative_last_name'];
    $config->legal_representative_document = $input['legal_representative_document'];
    $config->legal_representative_document_type = $input['legal_representative_document_type'];
    $config->legal_representative_sex = $input['legal_representative_sex'];
    $config->legal_representative_email = strtolower($input['legal_representative_email']);
    $config->legal_representative_tel = $input['legal_representative_tel'];

    $config->nit = $input['nit'];
    $config->nit_name = $input['nit_name'];
    $config->nit_date_of_renovation = $input['nit_date_of_renovation'] ? Carbon::createFromFormat('Y-m-d', $input['nit_date_of_renovation']) : null;

    $config->bank_name = $input['bank_name'];
    $config->bank_account_number = $input['bank_account_number'];
    $config->bank_account_type = $input['bank_account_type'];
    $config->bank_account_holder = $input['bank_account_holder'];
    $config->bank_account_holder_document = $input['bank_account_holder_document'];

    $config->save();

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

    if ($district) {
      $district->delete();
      $res->ok = true;
      $res->district = $district->toArray();
    } else {
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
