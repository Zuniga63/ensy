<?php

namespace App\Http\Controllers;

use App\Models\CountryDepartment;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerContact;
use App\Models\Customer\CustomerInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    $customers = Customer::orderBy('first_name')
      ->orderBy('last_name')
      ->with('information', 'contacts')
      ->withSum('invoices as balance', 'balance')
      ->get();

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
   * Se encarga de guardar la información de contacto del cliente.
   * @param \Illuminate\Http\Request  $request
   * @param  \App\Models\Customer\Customer  $customer
   * @return \Illuminate\Http\Response
   */
  public function storeCustomerContact(Request $request, Customer $customer)
  {
    $rules = [
      'description' => 'nullable|string|max:50',
      'number' => 'required|string|max:20',
      'whatsapp' => 'required|boolean'
    ];

    $attr = [
      'description' => 'descripción',
      'number' => 'numero',
    ];

    $request->validate($rules, [], $attr);

    $contact = new CustomerContact();
    $contact->description = $request->input('description');
    $contact->number = $request->input('number');
    $contact->whatsapp = $request->input('whatsapp', false);

    $customer->contacts()->save($contact);

    return Redirect::route('customer.edit', $customer->id);
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
    $customer->load('information', 'contacts');
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
   * Se encarga de actualizar la información bancaria
   * del cliente en la base de datos.
   */
  public function updateCustomerBankInformation(Request $request, Customer $customer)
  {
    $rules = [
      'id' => 'nullable|integer|exists:customer_information,id',
      'cashbox_id' => 'required|integer|exists:customer,id',
      'bank_name' => 'nullable|string|min:3|max:90',
      'bank_account_type' => 'nullable|string|in:savings,current',
      'bank_account_number' => 'nullable|string|max:255',
      'bank_account_holder' => 'nullable|string|min:3|max:90',
      'bank_account_holder_document' => 'nullable|string|max:20',
    ];

    $attr = [
      'id' => 'identificador de table',
      'cashbox_id' => 'identificador de caja',
      'bank_name' => 'nombre del banco',
      'bank_account_type' => 'tipo de cuenta',
      'bank_account_number' => 'numero de cuenta',
      'bank_account_holder' => 'titular de la cuenta',
      'bank_account_holder_document' => 'documento del titular'
    ];

    $request->validate($rules, [], $attr);
    try {
      DB::beginTransaction();
      $information = $customer->information;
      if (!$information) {
        $information = $this->createCustomerInformation($customer->id);
      }

      $information->bank_name = $request->input('bank_name');
      $information->bank_account_type = $request->input('bank_account_type');
      $information->bank_account_number = $request->input('bank_account_number');
      $information->bank_account_holder = $request->input('bank_account_holder');
      $information->bank_account_holder_document = $request->input('bank_account_holder_document');

      $customer->information()->save($information);
      DB::commit();
    } catch (\Throwable $th) {
      dd($th);
      DB::rollBack();
    }

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

  /**
   * Se encarga de guardar la información de contacto del cliente.
   * @param  \App\Models\Customer\CustomerContact  $customer_contact
   * @param  \App\Models\Customer\Customer  $customer
   * @return \Illuminate\Http\Response
   */
  public function destroyCustomerContact(Customer $customer, CustomerContact $customer_contact)
  {
    $customer_contact->delete();
    return Redirect::route('customer.edit', $customer->id);
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

  /**
   * Crea la relación con la tabla de información si esta no existe
   * @param integer $customerId Identificador del cliente
   * @return CustomerInformation
   */
  protected function createCustomerInformation($customerId)
  {
    $information = new CustomerInformation();
    /**
     * @var Customer
     */
    $customer = Customer::find($customerId);
    $customer->information()->save($information);

    return $information;
  }
}
