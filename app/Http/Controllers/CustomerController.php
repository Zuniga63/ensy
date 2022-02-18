<?php

namespace App\Http\Controllers;

use App\Models\CountryDepartment;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CustomerController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $customers = Customer::orderBy('first_name')->orderBy('last_name')->get();

    return Inertia::render('Customer/Index', compact('customers'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return Inertia::render('Customer/Create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $rules = $this->getCustomerRules();
    $attr = $this->getCustomerAttributes();

    $request->validate($rules, [], $attr);

    $inputs = $request->all();

    $customer = Customer::create($inputs);
    $result = [
      'ok' => true,
      'customer' => $customer
    ];

    if ($inputs['addOtherCustomer']) {
      $result['reload'] = true;
      return Redirect::route('customer.create')->with('message', $result);
    } else {
      return Redirect::route('customer.index')->with('message', $result);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Customer\Customer  $customer
   * @return \Illuminate\Http\Response
   */
  public function show(Customer $customer)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Customer\Customer  $customer
   * @return \Illuminate\Http\Response
   */
  public function edit(Customer $customer)
  {
    $departments = $this->getCountryDepartments();
    return Inertia::render('Customer/Edit', compact('departments', 'customer'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Customer\Customer  $customer
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Customer $customer)
  {
    $rules = $this->getCustomerRules($customer->id);
    $attr = $this->getCustomerAttributes();
    $request->validate($rules, [], $attr);

    $inputs = $request->all();

    //Se actualizan los datos del cliente

    $customer->first_name = $inputs['first_name'];
    $customer->last_name = $inputs['last_name'];
    $customer->email = $inputs['email'];
    $customer->sex = $inputs['sex'];
    $customer->document_number = $inputs['document_number'];
    $customer->document_type = $inputs['document_type'];
    $customer->save();

    return Redirect::route('customer.edit', $customer->id);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Customer\Customer  $customer
   * @return \Illuminate\Http\Response
   */
  public function destroy(Customer $customer)
  {
    $customer->delete();
    $res = [
      'ok' => true,
      'customer' => $customer->toArray()
    ];

    return $res;
  }

  //-----------------------------------------------------------
  //-----------------------------------------------------------
  // UTILIDADES
  //-----------------------------------------------------------
  //-----------------------------------------------------------

  /**
   * Estable ce las reglas de validación del firmulario
   * para crear o actualizar los datos del cliente
   * @param integer|null $customer_id Identificación del cliente
   */
  protected function getCustomerRules($customer_id = null)
  {
    $rules = [
      'first_name' => 'required|string|min:3|max:50',
      'last_name' => 'nullable|string|min:3|max:50',
      'email' => "nullable|string|email:rfc,dns|unique:customer,email," . $customer_id,
      'sex' => 'nullable|string|in:f,m',
      'document_number' => 'nullable|string|max:20|unique:customer,document_number,' . $customer_id,
      'document_type' => 'nullable|string|min:2|in:CC,CE,TI,NIT,NIP,PAP'
    ];

    return $rules;
  }

  protected function getCustomerAttributes()
  {
    return [
      'first_name' => 'nombres',
      'last_name' => 'apellidos',
      'email' => 'correo',
      'sex' => 'sexo',
      'document_number' => 'numero de documento',
      'document_type' => 'tipo de documento',
    ];
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
          ->orderBY('name');
      }
    ])->get(['id', 'name']);
  }
}
