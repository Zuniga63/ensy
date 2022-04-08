<?php

namespace App\Http\Controllers;

use App\Models\BusinessConfig;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Models\Customer\Customer;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceItem;
use App\Models\Invoice\InvoicePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class InvoiceController extends Controller
{
  protected $CODE_PREFIX = "invoice_payment_";
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $customers = $this->getCustomers();
    $boxs = Cashbox::orderBy('order')->get(['id', 'name',]);
    $config = $this->getInvoiceInformation();
    $invoices = Invoice::orderBy('expedition_date', 'ASC')->get();
    return Inertia::render('Invoice/Index', compact('customers', 'boxs', 'config', 'invoices'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //Area de validación
    $rules = $this->getRules();
    $attr = $this->getAttr();
    $messages = $this->getCustomMessage();
    $request->validate($rules, $messages, $attr);

    $date = Carbon::now();
    $invoiceItems = [];
    $invoicePayments = [];

    $subtotal = 0;
    $discount = 0;
    $invoiceAmount = 0;
    $cash = 0;
    $credit = 0;
    $change = 0;

    $items = $request->input('invoiceItems');
    $payments = $request->input('invoicePayments');

    /**
     * Se crean las instancias de los items que se van a registrar en la 
     * factura y se actualiza los valores de $subtotal, discount, invoiceAmount
     */
    foreach ($items as $key => $item) {
      $invoiceItem = new InvoiceItem();
      $invoiceItem->quantity = $item['quantity'];
      $invoiceItem->description = $item['description'];
      $invoiceItem->unit_value = $item['unitValue'];
      $invoiceItem->discount = $item['discount'] ? $item['discount'] : null;
      $invoiceItem->amount = $item['amount'];

      $invoiceItems[] = $invoiceItem;

      $subtotal += $item['quantity'] * $item['unitValue'];
      $discount += $item['discount'] * $item['quantity'];
    }

    $invoiceAmount = $subtotal - $discount;

    /**
     * Se crean las instancias de los pagos en efectivos
     * realizado por el clientes y actualización de la variabel $cash
     */
    foreach ($payments as $key => $payment) {
      $cash += $payment['amount'];
    }

    /**
     * Se establece si es una venta a credito, en efectivo o credito efectivo.
     * De la misma forma se define las transformaciónes a los pagos.
     */
    if ($invoiceAmount > $cash) {
      $credit = $invoiceAmount - $cash;
    } else {
      $change = $cash - $invoiceAmount;
    }

    //return $request->input('customerName', 'Mostrador');

    //----------------------------------------------------------------------------
    //EN ESTE PUNTO SE TERMINAN LAS TAREAS PRELIMINARES 
    //Y SE PROCEDE A GUARDAR LA INFORMACIÓN DE LA FACTURA EN LA BASE DE DATOS.
    //----------------------------------------------------------------------------

    /**
     * Si la factura tiene credito entonces se verifica si el cliente existe
     */
    $customerExists = Customer::where('id', $request->input('customerId'))->exists();
    if ($credit > 0 && !$customerExists) {
      return [
        'ok' => false,
        'message' => 'No se puede registrar creditos a clientes que no estén registrados.'
      ];
    }

    DB::beginTransaction();

    try {
      $invoiceNumber = Invoice::max('number');

      /** @var Invoice */
      $invoice = new Invoice();
      $invoice->prefix = null;
      $invoice->number = $invoiceNumber + 1;

      $invoice->seller_id = auth()->user()->id;
      $invoice->seller_name = auth()->user()->name;

      $invoice->customer_id = $request->input('customerId');
      $invoice->customer_name = $request->input('customerName') ? $request->input('customerName') : 'Mostrador';
      $invoice->customer_document = $request->input('customerDocument');
      $invoice->customer_address = $request->input('customerAddress');
      $invoice->customer_phone = $request->input('customerPhone');

      $invoice->expedition_date = Carbon::createFromFormat('Y-m-d', $request->input('expeditionDate'))->format('Y-m-d H:i');
      $invoice->expiration_date = Carbon::createFromFormat('Y-m-d', $request->input('expirationDate'))->format('Y-m-d H:i');

      $invoice->subtotal = $subtotal;
      $invoice->discount = $discount > 0 ? $discount : null;
      $invoice->amount = $invoiceAmount;
      $invoice->cash = $cash > 0 ? $cash : null;
      $invoice->credit = $credit > 0 ? $credit : null;
      $invoice->cash_change = $change > 0 ? $change : null;
      $invoice->balance = $credit > 0 ? $credit : null;

      $invoice->save();

      $invoice->items()->saveMany($invoiceItems);

      if ($cash > 0) {
        $balance = $invoiceAmount;

        foreach ($payments as $key => $payment) {
          $paymentAmount = $payment['amount'];

          if ($balance > 0) {
            $balance -= $paymentAmount;

            if ($balance < 0) {
              //?Es una suma porque el saldo queda negativo.
              $paymentAmount += $balance;
            }

            //Se crea la transacción completa
            $invoicePayment = new InvoicePayment();
            $invoicePayment->customer_id = $request->input('customerId');
            $invoicePayment->payment_date = $date->format('Y-m-d H:i');
            $invoicePayment->description = "Deposito en " . $payment['boxName'];
            $invoicePayment->amount = $paymentAmount;
            $invoicePayment->initial_payment = true;

            /**
             * Acontinuación se crea la transacción y elcodigo para que elpayment pueda rastrearlo.
             */

            if ($payment['registerTransaction']) {
              $code = uniqid($this->CODE_PREFIX);
              $invoicePayment->transaction_code = $code;

              //Se crea la transacción
              $transaction = new CashboxTransaction();
              $transaction->cashbox_id = $payment['boxId'];
              $transaction->transaction_date = $date->format('Y-m-d H:i');
              $transaction->amount = $paymentAmount;
              $transaction->code = $code;
              $transaction->blocked = true;

              if ($credit) {
                $transaction->description = "Abono a la factura N° $invoice->number";
              } else {
                $transaction->description = "Cancelación de la factura N° $invoice->number";
              }

              $transaction->save();
            }

            $invoicePayments[] = $invoicePayment;
          } else {
            break;
          }
        }

        //Finalmente se guardan los pagos en la factura
        $invoice->payments()->saveMany($invoicePayments);
      }


      $invoice->refresh();

      DB::commit();
    } catch (\Throwable $th) {
      DB::rollBack();
      return $th;
    }

    return [
      'ok' => true,
      'invoice' => $invoice,
    ];
  }

  public function storePayments(Request $request)
  {
    $rules = [
      'invoiceId' => 'required|integer|exists:invoice,id',
      'customerId' => 'required|integer|exists:customer,id',
      'invoicePayments.*' => 'array:boxId,boxName,registerTransaction,amount',
      'invoicePayments.*.boxId' => 'required|integer|exists:cashbox,id',
      'invoicePayments.*.boxName' => 'required|string',
      'invoicePayments.*.registerTransaction' => 'required|boolean',
      'invoicePayments.*.amount' => 'required|numeric|min:1|max:99999999.99',
    ];

    $request->validate($rules);

    //get the invoice intance
    /** @var Invoice */
    $invoice = Invoice::find($request->input('invoiceId'));

    if ($invoice->balance) {
      $paymentAmount = array_reduce($request->input('payments'), function ($carry, $item) {
        $carry += $item['amount'];
        return $carry;
      }, 0);

      $balanceIsCancelled = bccomp($invoice->balance, $paymentAmount) > 0 ? false : true;

      DB::beginTransaction();
      try {
        foreach ($request->input('payments') as $key => $paymentItem) {
          $amount = $paymentItem['amount'];
          $date = Carbon::now();

          /**
           * * bccomp compara dos cadenas numericas y retorna:
           * * 1: Cuando el priemro es mayor que el segundo
           * * -1: Cuando el segundo es mayor que el primero
           * * 0: Cuando son iguales.
           */

          if (bccomp($invoice->balance, $amount) > 0) {
            //*Se crean las instancias
            $invoicePayment = new InvoicePayment([
              'customer_id' => $request->input('customerId'),
              'payment_date' => $date->format('Y-m-d H:i'),
              'description' => "Deposito en " . $paymentItem['boxName'],
              'amount' => $amount,
              'transaction_code' => null,
            ]);

            if ($paymentItem['registerTransaction']) {
              $code = uniqid($this->CODE_PREFIX);
              $description = $balanceIsCancelled
                ? "Cancelación de la factura N° $invoice->number"
                : "Abono a la factura N° $invoice->number";

              /** @var CashboxTransaction */
              $transaction = new CashboxTransaction([
                'cashbox_id' => $paymentItem['boxId'],
                'transaction_date' => $date->format('Y-m-d H:i'),
                'description' => $description,
                'amount' => $amount,
                'code' => $code,
                'blocked' => true,
              ]);

              $invoicePayment->transaction_code = $code;
              $transaction->save();
            };

            $invoice->payments()->save($invoicePayment);
            $invoice->balance = bcsub($invoice->balance, $invoicePayment->amount);
            $invoice->save();
          } else {
            $invoicePayment = new InvoicePayment([
              'customer_id' => $request->input('customerId'),
              'payment_date' => $date->format('Y-m-d H:i'),
              'description' => "Deposito en " . $paymentItem['boxName'],
              'amount' => $invoice->balance,
              'transaction_code' => null,
            ]);

            if ($paymentItem['registerTransaction']) {
              $code = uniqid($this->CODE_PREFIX);
              $description = $balanceIsCancelled
                ? "Cancelación de la factura N° $invoice->number"
                : "Abono a la factura N° $invoice->number";

              /** @var CashboxTransaction */
              $transaction = new CashboxTransaction([
                'cashbox_id' => $paymentItem['boxId'],
                'transaction_date' => $date->format('Y-m-d H:i'),
                'description' => $description,
                'amount' => $invoice->balance,
                'code' => $code,
                'blocked' => true,
              ]);

              $invoicePayment->transaction_code = $code;
              $transaction->save();
            };

            $invoice->payments()->save($invoicePayment);
            $invoice->balance = null;
            $invoice->save();

            break;
          }
        } //.end forEach

        $invoice->refresh();

        DB::commit();
      } catch (\Throwable $th) {
        DB::rollBack();
        return [
          'ok' => false,
          'error' => $th
        ];
      }

      return [
        'ok' => true,
        'invoice' => $invoice,
      ];
    } else {
      return [
        'ok' => false,
        'invoice' => $invoice
      ];
    }
  }

  public function cancelPayment(Request $request)
  {
    $rules = [
      'invoiceId' => 'required|integer|exists:invoice,id',
      'paymentId' => 'required|integer|exists:invoice_payment,id',
      'message' => 'required|string|min:3',
      'password' => 'required|string|min:3',
      'password' => ['required', 'string', 'min:3', function ($attr, $value, $fail) {
        if (!Hash::check($value, auth()->user()->password)) {
          return $fail('La contraseña es incorrecta.');
        }
      }]
    ];

    $attr = [
      'password' => 'contraseña',
      'message' => 'motivo',
    ];

    $messages = [
      'password.required' => 'Se requiere su contraseña para continuar.',
      'message.required' => "Se requiere un motivo para realizar la cancelación"
    ];

    $request->validate($rules, $messages, $attr);

    $ok = false;
    $message = null;
    $error = null;

    //Recupero la factura
    $invoice = Invoice::find($request->invoiceId);
    //Se recupera el pago
    $payment = InvoicePayment::find($request->paymentId);

    if (!$payment->cancel) {
      //Se actualiza el saldo de la factura y el dinero
      $invoice->balance = bcadd($invoice->balance, $payment->amount);
      if ($payment->initial_payment) {
        $invoice->cash = bcsub($invoice->cash, $payment->amount);
        $invoice->credit = bcadd($invoice->credit, $payment->amount);
      }
      //Se cancela el pago
      $payment->cancel = true;
      $payment->cancel_message = auth()->user()->name . ": " . $request->input('message');

      DB::beginTransaction();

      try {
        //Se verifica si el pago tiene asociada una transacción
        if ($payment->transaction_code) {
          /** @var CashboxTransaction */
          $transaction = CashboxTransaction::where('code', $payment->transaction_code)->first();
          if ($transaction) {
            $transaction->delete();
          }
          $payment->transaction_code = null;
        }

        $invoice->save();
        $payment->save();

        DB::commit();
        $ok = true;
      } catch (\Throwable $th) {
        DB::rollBack();
        $message = "Error al guardar en la base de datos.";
        $error = $th->getMessage();
        //throw $th;
      }
    } else {
      $message = "¡El pago ya fue cancelado.!";
    }

    return [
      'ok' => $ok,
      'invoice' => $invoice,
      'message' => $message,
      'error' => $error
    ];
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Invoice\Invoice  $invoice
   * @return \Illuminate\Http\Response
   */
  public function show(Invoice $invoice)
  {
    $invoice->load(['customer', 'items', 'payments']);

    return $invoice;
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Invoice\Invoice  $invoice
   * @return \Illuminate\Http\Response
   */
  public function edit(Invoice $invoice)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Invoice\Invoice  $invoice
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Invoice $invoice)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Invoice\Invoice  $invoice
   * @return \Illuminate\Http\Response
   */
  public function destroy(Invoice $invoice)
  {
    //
  }

  //------------------------------------------------------------------------
  //  UTILITIES
  //------------------------------------------------------------------------
  protected function getCustomers()
  {
    return Customer::orderBy('first_name')
      ->orderBy('last_name')
      ->with('contacts')
      ->get([
        'id',
        'first_name',
        'last_name',
        'document_number',
        'document_type',
      ]);
  }

  protected function getInvoiceInformation()
  {
    /**
     * @var BusinessConfig
     */
    $config = BusinessConfig::first();

    $invoiceNumber = Invoice::max('number');

    return [
      'id' => $config->id,
      'name' => $config->name,
      'logo' => $config->logo_url,
      'nit' => $config->nit,
      'phone' => [
        'number' => $config->phone,
        'show' => $config->show_phone ? true : false,
      ],
      'address' => [
        'value' => $config->address,
        'show' => $config->show_address ? true : false,
      ],
      'email' => [
        'value' => $config->email,
        'show' => $config->show_email ? true : false,
      ],
      'whatsapp' => [
        'number' => $config->whatsapp,
        'show' => $config->show_whatsapp ? true : false,
      ],
      'facebook' => [
        'nick' => $config->facebook_nick,
        'link' => $config->facebook_link,
        'show' => $config->show_facebook ? true : false,
      ],
      'instagram' => [
        'nick' => $config->instagram_nick,
        'link' => $config->instagram_link,
        'show' => $config->show_instagram ? true : false,
      ],
      "legalRepresentative" => [
        'firstName' => $config->legal_representative_first_name,
        'lastName' => $config->legal_representative_last_name,
        'document' => $config->legal_representative_document,
        'documentType' => $config->legal_representative_document_type,
        'phone' => $config->legal_reprentative_tel,
        'email' => $config->legal_representative_email
      ],
      "bankAccount" => [
        'name' => $config->bank_name,
        'number' => $config->bank_account_number,
        'type' => $config->bank_account_type,
        'holder' => [
          'name' => $config->bank_account_holder,
          'document' => $config->bank_account_holder_document,
        ],
      ],
      "invoiceNumber" => $invoiceNumber + 1,
      "seller" => auth()->user()->name,
    ];
  }

  protected function getRules()
  {
    return [
      'customerId' => 'nullable|integer|exists:customer,id',
      'customerName' => 'nullable|string|min:3|max:90',
      'customerDocument' => 'nullable|string|min:6|max:45',
      'customerAddress' => 'nullable|string|min:3|max:150',
      'customerPhone' => 'nullable|string|min:6|max:20',
      'expeditionDate' => 'required|date',
      'expirationDate' => 'required|string|date|after_or_equal:expeditionDate',
      'invoiceItems' => 'required|array|min:1',
      'invoiceItems.*' => 'array:quantity,description,unitValue,discount,amount',
      'invoiceItems.*.quantity' => 'required|numeric|min:0.0001|max:9999.9999',
      'invoiceItems.*.description' => 'required|string|min:3|max:255',
      'invoiceItems.*.unitValue' => 'required|numeric|min:1|max:99999999.99',
      'invoiceItems.*.discount' => 'nullable|numeric|min:0|max:99999999.99',
      'invoiceItems.*.amount' => 'required|numeric|min:1|max:99999999.99',
      'invoicePayments' => 'nullable|array',
      'invoicePayments.*' => 'array:boxId,boxName,registerTransaction,useForChange,amount',
      'invoicePayments.*.boxId' => 'required|integer|exists:cashbox,id',
      'invoicePayments.*.boxName' => 'required|string',
      'invoicePayments.*.registerTransaction' => 'required|boolean',
      'invoicePayments.*.useForChange' => 'required|boolean',
      'invoicePayments.*.amount' => 'required|numeric|min:1|max:99999999.99',
    ];
  }

  protected function getAttr()
  {
    return [
      'customerId' => 'cliente',
      'customerName' => 'cliente',
      'customerDocument' => 'nit',
      'customerAddress' => 'direccíon',
      'customerPhone' => 'telefono',
      'expeditionDate' => 'fecha de expedición',
      'expirationDate' => 'fecha de vencimiento',
      'invoiceItems' => 'listado de articulos',
    ];
  }

  protected function getCustomMessage()
  {
    return [
      'invoiceItems.array' => 'Debe ser un listado de items.',
    ];
  }
}
