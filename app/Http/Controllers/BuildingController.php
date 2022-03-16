<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\BuildingAdmin;
use App\Models\CountryDepartment;
use App\Models\Customer\Customer;
use App\Models\TownDistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use phpDocumentor\Reflection\Types\Boolean;

class BuildingController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $buildings = Building::orderBy('code')->get();

    return Inertia::render('Building/Index', compact('buildings'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $customers = Customer::orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'document_number', 'document_type']);
    $admins = BuildingAdmin::orderBy('name')->get();
    $departments = $this->getCountryDepartments();
    $allDistricts = TownDistrict::orderBy('name')->with('town')->get();
    $code = Building::max('code') + 1;


    return Inertia::render('Building/Create', compact('customers', 'admins', 'departments', 'allDistricts', 'code'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $rules = $this->getBuildingRules();
    $attr = $this->getBuildingAttr();
    $message = $this->getCustomMessages();

    $request->validate($rules, $message, $attr);
    $imagePath = null;
    $input = $request->all();

    try {
      //Se guarda el archivo del inmueble si existe
      if ($request->hasFile('image')) {
        //Recupero la información de la dirección
        $address = $request->input('address');
        $prefix = null;

        $district_name = "barrio_desconocido";
        $slug_address = "dirección_desconocida";
        $separator = "_";

        if ($address) {
          //Se comprueba si el barrio existe
          if (array_key_exists('district', $address)) {
            //Se comprueba que la clave del nombre del barrio existe
            if (array_key_exists('name', $address['district'])) {
              $district_name = $address['district']['name']
                ? str_replace(' ', $separator, $address['district']['name'])
                : $district_name;
            }

            //Se comprueba que la clave de la dirección existe
            if (array_key_exists('address', $address)) {
              $slug_address = $address['address']
                ? str_replace(' ', $separator, $address['address'])
                : $slug_address;
            }

            //Se minimiza la letra
            $district_name = strtolower($district_name);
            $slug_address = strtolower($slug_address);
          }
        }

        $prefix = $district_name . $separator . $slug_address . $separator;

        $imageName = uniqid($prefix) . '.' . $request->image->extension();
        $imagePath = $request->image->storeAs('building', $imageName, 'public');
      }

      //Se convierte en fracción las comisiones
      $input['commission'] = floatval($input['commission']) / 100;
      $input['insurance_commission'] = floatval($input['insurance_commission']) / 100;

      //Se guarda la ruta del archivo
      $input['image_path'] = $imagePath;

      //Se define si el inmueble está asegurado
      if (!$input['insured']) {
        $input['insurance_carrier'] = null;
        $input['insurance_type'] = null;
        $input['insurance_commission'] = 0;
      }

      //Se guarda el inmueble en la base de datos
      $building = Building::create($input);

      $result = [
        'ok' => true,
        'building' => $building,
      ];

      if ($input['addOther']) {
        $result['reload'] = true;
        return Redirect::route('building.create')->with('message', $result);
      } else {
        return Redirect::route('building.index')->with('message', $result);
      }
    } catch (\Throwable $th) {
      //throw $th;

      //Se elimina la imagen si se guardó
      if ($imagePath && Storage::disk('public')->exists($imagePath)) {
        Storage::disk('public')->delete($imagePath);
      }
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Building  $building
   * @return \Illuminate\Http\Response
   */
  public function show(Building $building)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Building  $building
   * @return \Illuminate\Http\Response
   */
  public function edit(Building $building)
  {
    $customers = Customer::orderBy('first_name')->orderBy('last_name')->get(['id', 'first_name', 'last_name', 'document_number', 'document_type']);
    $admins = BuildingAdmin::orderBy('name')->get();
    $departments = $this->getCountryDepartments();
    $allDistricts = TownDistrict::orderBy('name')->with('town')->get();

    return Inertia::render('Building/Edit', compact('building', 'customers', 'admins', 'departments', 'allDistricts'));
  }

  /**
   * Update the general information of building.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Building  $building
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Building $building)
  {
    $rules = $this->getBuildingRules('general');
    $rules['deleteImage'] = 'required|boolean';
    $attr = $this->getBuildingAttr();
    $messages = $this->getCustomMessages();
    $imagePath = null;
    $log = [];

    $request->validate($rules, $messages, $attr);

    try {
      //Se verifica si se elimina la imagen actual del inmueble
      if ($building->image_path && ($request->deleteImage || $request->hasFile('image'))) {
        $log[] = "La imagen actual se va a eliminar.";
        if (Storage::disk('public')->exists($building->image_path)) {
          $log[] = "La imagen existe y se procede a eliminar.";
          Storage::disk('public')->delete($building->image_path);
        }

        $building->image_path = null;
        $log[] = "La imagen se eliminó correctamente.";
      }

      //Se guarda el archivo del inmueble si existe
      if ($request->hasFile('image')) {
        $log[] = "Se procede a guardar la imagen nueva del inmueble.";
        //Recupero la información de la dirección
        $address = $building->address;
        $prefix = null;

        $district_name = "barrio_desconocido";
        $slug_address = "dirección_desconocida";
        $separator = "_";

        if ($address) {
          //Se comprueba si el barrio existe
          if (array_key_exists('district', $address)) {
            //Se comprueba que la clave del nombre del barrio existe
            if (array_key_exists('name', $address['district'])) {
              $district_name = $address['district']['name']
                ? str_replace(' ', $separator, $address['district']['name'])
                : $district_name;
            }

            //Se comprueba que la clave de la dirección existe
            if (array_key_exists('address', $address)) {
              $slug_address = $address['address']
                ? str_replace(' ', $separator, $address['address'])
                : $slug_address;
            }

            //Se minimiza la letra
            $district_name = strtolower($district_name);
            $slug_address = strtolower($slug_address);
          }
        }

        $prefix = $district_name . $separator . $slug_address . $separator;

        $imageName = uniqid($prefix) . '.' . $request->image->extension();
        $imagePath = $request->image->storeAs('building', $imageName, 'public');

        $building->image_path = $imagePath;
        $log[] = "La imagen fue guardada correctamente.";
      }

      $building->description = $request->input('description');
      $building->available = $request->input('available');
      $building->rooms = $request->input('rooms');
      $building->bathrooms = $request->input('bathrooms');
      $building->parking_lots = $request->input('parking_lots');
      $building->socioeconomic = $request->input('socioeconomic');
      $building->area = $request->input('area');
      $building->private_area = $request->input('private_area');
      $building->floor = $request->input('floor');
      $building->antiquity = $request->input('antiquity');
      $building->features = $request->input('features');
      $building->save();
      $log[] = "La información del inmueble fue actualizada.";
    } catch (\Throwable $th) {
      //Se elimina la imagen si se guardó
      if ($imagePath && Storage::disk('public')->exists($imagePath)) {
        Storage::disk('public')->delete($imagePath);
      }

      throw $th;
    }

    return Redirect::route('building.edit', $building->id)->with('message', $log);
  }

  /**
   * Update the address of building
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Building  $building
   * @return \Illuminate\Http\Response
   */
  public function updateAddress(Request $request, Building $building)
  {
    $rules = $this->getBuildingRules('address');
    $attr = $this->getBuildingAttr();
    $messages = $this->getCustomMessages();
    $imagePath = null;

    $request->validate($rules, $messages, $attr);

    //Se cambia el nombre de la imagen
    if ($building->image_path) {
      //Recupero la información de la dirección
      $address = $request->input('address');
      $prefix = null;

      $district_name = "barrio_desconocido";
      $slug_address = "dirección_desconocida";
      $separator = "_";

      if ($address) {
        //Se comprueba si el barrio existe
        if (array_key_exists('district', $address)) {
          //Se comprueba que la clave del nombre del barrio existe
          if (array_key_exists('name', $address['district'])) {
            $district_name = $address['district']['name']
              ? str_replace(' ', $separator, $address['district']['name'])
              : $district_name;
          }

          //Se comprueba que la clave de la dirección existe
          if (array_key_exists('address', $address)) {
            $slug_address = $address['address']
              ? str_replace(' ', $separator, $address['address'])
              : $slug_address;
          }

          //Se minimiza la letra
          $district_name = strtolower($district_name);
          $slug_address = strtolower($slug_address);
        }
      }

      $prefix = $district_name . $separator . $slug_address . $separator;

      if (Storage::disk('public')->exists($building->image_path)) {
        //Se obtiene la información del archivo almacenado
        $infoPath = pathinfo(public_path("storage/$building->image_path"));
        //Se establece el nombre del nuevo directorio
        $imageName = uniqid($prefix) . '.' . $infoPath['extension'];
        $imagePath = "building/$imageName";
        //Se cambia el fichero
        Storage::disk('public')->move($building->image_path, $imagePath);
      }

      $building->image_path = $imagePath;
    }

    $building->building_admin_id = $request->input('building_admin_id');
    $building->building_type = $request->input('building_type');
    $building->country_department_id = $request->input("country_department_id");
    $building->town_id = $request->input('town_id');
    $building->town_district_id = $request->input('town_district_id');
    $building->address = $request->input('address');

    //Si el inmueble es null entonces se resetea el valor de la cuota de administración
    if (!$building->building_admin_id) {
      $building->admin_fee = 0;
    }

    $building->save();

    return Redirect::route('building.edit', $building->id);
  }

  public function updateBuildingState(Request $request, Building $building)
  {
    $rules = $this->getBuildingRules('state', $building->id);
    $attr = $this->getBuildingAttr();
    $messages = $this->getCustomMessages();

    $request->validate($rules, $messages, $attr);

    $building->owner_id = $request->input('owner_id');
    $building->lease_fee = $request->input('lease_fee', 0.00);
    $building->commission = $request->input('commission') ? floatval($request->input('commission')) / 100 : 0;
    $building->insured = $request->boolean('insured');

    if ($building->insured) {
      $building->insurance_carrier = $request->input('insurance_carrier');
      $building->insurance_type = $request->input('insurance_type');
      $building->insurance_commission = $request->input('insurance_commission')
        ? floatval($request->input('insurance_commission')) / 100
        : 0;
    } else {
      $building->insurance_carrier = null;
      $building->insurance_type = null;
      $building->insurance_commission = 0;
    }

    $building->admin_fee = $request->input('admin_fee', 0.00);
    $building->code = $request->input('code');
    $building->mandate_contract = $request->input('mandate_contract', false);

    $building->save();

    return Redirect::route('building.edit', $building->id);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Building  $building
   * @return \Illuminate\Http\Response
   */
  public function destroy(Building $building)
  {
    //Se elimina la imagen del inmueble
    if ($building->image_path && Storage::disk('public')->exists($building->image_path)) {
      Storage::disk('public')->delete($building->image_path);
    }

    $building->delete();

    $res = [
      'ok' => true,
      'building' => $building->toArray()
    ];

    return $res;
  }

  //-------------------------------------------------------------------------------
  //-------------------------------------------------------------------------------
  //  UTILIDADES
  //-------------------------------------------------------------------------------
  //-------------------------------------------------------------------------------

  /**
   * Se encarga de consultar la base de datos
   * y recuperar la informacion de los departamentos, con sus ciudades 
   * y distritos
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
   * Construye las reglas de validación para guardar o actualizar. Por defecto retorna todas ls reglas.
   * @param NULL|String $section La sección [general,address,state] que se quiere construir.
   * @return Array
   */
  protected function getBuildingRules($section = null, $buildingId = null)
  {
    $generalRules = [
      'image' => 'nullable|file|image|max:1024',
      'description' => 'nullable|string|max:255',
      'rooms' => 'nullable|integer|min:0|max:100',
      'bathrooms' => 'nullable|integer|min:0|max:100',
      'parking_lots' => 'nullable|integer|min:0|max:100',
      'socioeconomic' => 'nullable|integer|min:0|max:100',
      'area' => 'nullable|numeric|min:0|max:999999',
      'private_area' => 'nullable|numeric|min:0|max:999999',
      'floor' => 'nullable|integer|min:1|max:100',
      'antiquity' => 'nullable|string|date',
      'features' => 'nullable|array',
    ];

    $addressRules = [
      'building_admin_id' => 'nullable|integer|exists:building_admin,id',
      'building_type' => 'nullable|string|in:house,apartment,business',
      'country_department_id' => 'required|integer|exists:country_department,id',
      'town_id' => 'required|integer|exists:town,id',
      'town_district_id' => 'required|exists:town_district,id',
      'address' => 'required|array:department,town,district,address,observation,apartment',
    ];

    $stateRules = [
      'owner_id' => 'nullable|integer|exists:customer,id',
      'lease_fee' => 'required|numeric|min:0|max:99999999.99',
      'commission' => 'required|integer|min:0|max:100',
      'admin_fee' => 'required|numeric|min:0|max:99999999.99',
      'insured' => 'required|boolean',
      'insurance_carrier' => 'nullable|string|max:45',
      'insurance_type' => 'nullable|string|max:45',
      'insurance_commission' => 'required|integer|min:0|max:100',
      'code' => 'nullable|integer|min:0|max:32000|unique:building,code,' . $buildingId,
      'available' => 'required|boolean',
      'mandate_contract' => 'boolean',
    ];


    if ($section === 'general') {
      return $generalRules;
    } else if ($section === 'address') {
      return $addressRules;
    } elseif ($section === 'state') {
      return $stateRules;
    } else {
      return array_merge($generalRules, $addressRules, $stateRules);
    }
  }

  /**
   * Construye los nombres personalizados de los atributos
   * @return Array
   */
  protected function getBuildingAttr()
  {
    return [
      'owner_id' => 'propietario',
      'country_department_id' => 'departamnto',
      'town_id' => 'municipio',
      'town_district_id' => 'barrio',
      'image' => 'imagen',
      'description' => 'descripción',
      'features' => 'caracteristicas',
      'address' => 'dirección',
      'building_type' => 'tipo de inmueble',
      'socioeconomic' => 'estrato',
      'rooms' => 'habitaciones',
      'bathrooms' => 'baños',
      'parking_lots' => 'parqueaderos',
      'area' => 'área',
      'private_area' => 'área privada',
      'floor' => 'piso',
      'antiquity' => 'antiguedad',
      'lease_fee' => 'canon de arrendamiento',
      'admin_fee' => 'cuota de administración',
      'commission' => 'comisión',
      'insurance_carrier' => 'aseguradora',
      'insurance_commission' => 'comisión',
      'insurance_type' => 'tipo de seguro',
      'available' => 'disponible',
    ];
  }

  /**
   * Construye los mensajes personalizados para algunos de los
   * campos del formulario.
   */
  protected function getCustomMessages()
  {
    return [
      'image.max' => 'La imagen debe ser maximo de 1MB',
      'lease_fee.max' => 'El canon de arrendamiento debe ser menor que $99.999.999',
      'admin_fee.max' => 'La cuota de administración debe ser menor que $99.999.999',
      'commission.min' => 'No puede ser menor que 0%.',
      'commission.max' => 'No puede ser mayor que 100%',
      'insurance_commission.min' => 'No puede ser menor que 0%',
      'insurance_commission.max' => 'No puede ser mayor que el 100%',
      'code.unique' => 'Ya fue tomado.',
    ];
  }
}
