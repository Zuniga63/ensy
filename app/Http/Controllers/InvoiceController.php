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
use stdClass;

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
    $invoices = Invoice::orderBy('id', 'ASC')->without('items')->get([
      'id',
      'customer_name',
      'seller_name',
      'prefix',
      'number',
      'expedition_date',
      'amount',
      'cash',
      'credit',
      'cash_change',
      'balance',
      'cancel',
      'cancel_message',
    ]);
    $reports = [
      'weeklyReport' => $this->getWeeklyReport(),
    ];
    return Inertia::render('Invoice/Index', compact('customers', 'boxs', 'config', 'invoices', 'reports'));
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
    $request->validate(
      $this->getRules(),
      $this->getCustomMessage(),
      $this->getAttr()
    );

    /**
     * *Necesario para registrar los pagos iniciales y la factura
     * @var Carbon
     */
    $expeditionDate = Carbon::createFromFormat('Y-m-d', $request->expeditionDate);
    /** 
     * *Necesario para registrar las transacciones
     * @param Carbon
     */
    $now = Carbon::now();
    /**
     * Formato con el que se guardan las fechas en la base de datos
     * @var String
     */
    $dateFormat = "Y-m-d H:i";

    /**
     * Instancia de la factura
     * @var Invoice
     */
    $invoice = new Invoice([
      'customer_id' => $request->input('customerId'),
      'seller_id' => auth()->user()->id,
      'prefix' => null,
      'number' => null,
      'expedition_date' => $expeditionDate->format($dateFormat),
      'expiration_date' => Carbon::createFromFormat('Y-m-d', $request->expirationDate)->format($dateFormat),
      'seller_name' => auth()->user()->name,
      'customer_name' => $request->input('customerName') ? $request->input('customerName') : 'Mostrador',
      'customer_document' => $request->input('customerDocument'),
      'customer_address' => $request->input('customerAddress'),
      'customer_phone' => $request->input('customerPhone'),
      'subtotal' => '0',
      'discount' => null,
      'amount' => '0',
      'cash' => null,
      'credit' => null,
      'cash_change' => null,
      'balance' => null,
      'items' => [],
      'payments' => [],
    ]);

    $items = [];
    $payments = [];

    /**
     * Se crean las instancias de los items que se van a registrar en la 
     * factura y se actualiza los valores de $subtotal, discount, amount
     */
    foreach ($request->input('invoiceItems') as $key => $item) {
      $subtotal = bcmul($item['unitValue'], $item['quantity'], 2);
      $discount = bcmul($item['discount'], $item['quantity'], 2);
      $amount = bcsub($subtotal, $discount);

      //Se agrega al 
      $items[] = new InvoiceItem([
        'quantity' => $item['quantity'],
        'description' => $item['description'],
        'unit_value' => $item['unitValue'],
        'discount' => $item['discount'] ? $item['discount'] : null,
        'amount' => $amount,
      ]);

      $invoice->subtotal = bcadd($invoice->subtotal, $subtotal, 2);
      $invoice->discount = bcadd($invoice->discount, $discount, 2);
      $invoice->amount = bcadd($invoice->amount, $amount, 2);
      $invoice->credit = bcadd($invoice->credit, $amount, 2);
    }

    DB::beginTransaction();

    try {
      //Se define el numero de la factura.
      $invoice->number = Invoice::max('number') + 1;
      /**
       * Valor maximo que puede registrarse con los pagos.
       * @var String
       */
      $paymentTop = $invoice->amount;

      /**
       * Se procede a actualizar los valores de la factura para
       * cash, credit, cash_change y balance. Al igual que a registrar
       * los pagos que están por debajo del top.
       */
      foreach ($request->input('invoicePayments') as $key => $value) {
        $amount = $value['amount'];

        //Se actualizan los parametros de la factura.
        $invoice->cash = bcadd($invoice->cash, $amount, 2);
        $comp = bccomp($invoice->cash, $invoice->amount, 2);

        if ($comp >= 0) {
          //*En este punto el dinero en efectivo es mayor o igaul al valor de la factura
          $invoice->credit = null;
          //*Si es mayor entonces se calcula el cambio que otorga.
          if ($comp > 0) $invoice->cash_change = bcsub($invoice->cash, $invoice->amount, 2);
        } else {
          //* En este punto el dinero en efectivo es menor que el valor de la factura.
          $invoice->credit = bcsub($invoice->credit, $amount, 2);
        }

        //Se registran los pagos si están dentro del tope
        if (bccomp($paymentTop, "0", 2) > 0) {
          //Se actualiza el tope
          $paymentTop = bcsub($paymentTop, $amount, 2);

          //Si el nuevo tope es negativo se debe corregir el valor del importe
          if (bccomp($paymentTop, '0', 2) < 0) $amount = bcadd($amount, $paymentTop);

          //Se registra la transacción si el import es mayor que cero
          if (bccomp($amount, "0", 2) > 0) {
            $payment = new InvoicePayment([
              'customer_id' => $request->input('customerId'),
              'transaction_id' => null,
              'payment_date' => $expeditionDate->format($dateFormat),
              'description' => "Deposito en " . $value['boxName'],
              'amount' => $amount,
              'initial_payment' => true,
            ]);

            //Se registra la transacción
            if ($value['registerTransaction']) {
              $transanction = new CashboxTransaction([
                'cashbox_id' => $value['boxId'],
                'transaction_date' => $now->format($dateFormat),
                'description' => "Venta: Pago inicial a la factura N° $invoice->number",
                'amount' => $amount,
                'blocked' => true,
              ]);

              if ($transanction->save()) {
                $payment->transaction()->associate($transanction);
              }
            }

            //Se guarda en el listado.
            $payments[] = $payment;
          }
        }
      }

      //* Se establece el saldo igual al credito
      $invoice->balance = $invoice->credit;

      /**
       * Se verifica que si la factura tiene credito, entonces el cliente 
       * debe estar registrado en la base de datos.
       */
      if ($invoice->credit && !$invoice->customer_id) {
        DB::rollBack();
        return [
          'ok' => false,
          'message' => 'No se puede registrar creditos a clientes que no estén registrados.'
        ];
      }

      /**
       * Se guarda el cliente si esté no se encuentra en el sistem
       */
      if (!$request->customerId && $request->customerName && $request->customerDocument) {
        if (Customer::where('document_number', $request->customerDocument)->doesntExist()) {
          //Se divide el nombre
          $names = explode(" ", $request->customerName);
          $count = count($names);
          $firstName = null;
          $lastName = null;

          if ($count === 1) {
            $firstName = $names[0];
          } else if ($count === 2) {
            $firstName = $names[0];
            $lastName = $names[1];
          } else if ($count === 3) {
            $firstName = $names[0] . " " . $names[1];
            $lastName = $names[2];
          } else if ($count === 4) {
            $firstName = "$names[0] $names[1]";
            $lastName = "$names[2] $names[3]";
          } else {
            $firstName = "$names[0] $names[1]";
            $lastName = implode(" ", array_slice($names, 2));
          }

          $customer = new Customer([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'document_number' => $request->customerDocument
          ]);

          if ($customer->save()) {
            $invoice->customer_id = $customer->id;
            array_map(function ($payment) use ($customer) {
              $payment->customer_id = $customer->id;
            }, $payments);
          }
        }
      }


      //Se guarda el modelo
      $invoice->save();
      $invoice->items()->saveMany($items);
      $invoice->payments()->saveMany($payments);

      DB::commit();
    } catch (\Throwable $th) {
      DB::rollBack();
      return $th;
    }

    return [
      'ok' => true,
      'invoice' => $invoice,
      'newNumber' => Invoice::max('number') + 1,
    ];
  }

  public function storePayments(Request $request)
  {
    $request->validate($this->getRules('add-payment'));

    //get the invoice intance
    /** @var Invoice */
    $invoice = Invoice::find($request->input('invoiceId'));

    if ($invoice->balance && !$invoice->cancel && $invoice->customer_id) {
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
              'transaction_id' => null,
              'payment_date' => $date->format('Y-m-d H:i'),
              'description' => "Deposito en " . $paymentItem['boxName'],
              'amount' => $amount,
              /* 'transaction_code' => null, */
            ]);

            if ($paymentItem['registerTransaction']) {
              /* $code = uniqid($this->CODE_PREFIX); */
              $description = $balanceIsCancelled
                ? "Abono: Cancelación del saldo de la factura N° $invoice->number"
                : "Abono: Pago a la factura N° $invoice->number";

              /** @var CashboxTransaction */
              $transaction = new CashboxTransaction([
                'cashbox_id' => $paymentItem['boxId'],
                'transaction_date' => $date->format('Y-m-d H:i'),
                'description' => $description,
                'amount' => $amount,
                'blocked' => true,
              ]);

              $transaction->save();
              $invoicePayment->transaction_id = $transaction->id;
            };

            $invoice->payments()->save($invoicePayment);
            $invoice->balance = bcsub($invoice->balance, $invoicePayment->amount);
            $invoice->save();
          } else {
            $invoicePayment = new InvoicePayment([
              'customer_id' => $request->input('customerId'),
              'transaction_id' => null,
              'payment_date' => $date->format('Y-m-d H:i'),
              'description' => "Deposito en " . $paymentItem['boxName'],
              'amount' => $invoice->balance,
            ]);

            if ($paymentItem['registerTransaction']) {
              /* $code = uniqid($this->CODE_PREFIX); */
              $description = $balanceIsCancelled
                ? "Cancelación de la factura N° $invoice->number"
                : "Abono a la factura N° $invoice->number";

              /** @var CashboxTransaction */
              $transaction = new CashboxTransaction([
                'cashbox_id' => $paymentItem['boxId'],
                'transaction_date' => $date->format('Y-m-d H:i'),
                'description' => $description,
                'amount' => $invoice->balance,
                'blocked' => true,
              ]);

              $transaction->save();
              $invoicePayment->transaction_id = $transaction->id;
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
          'error' => $th->getMessage(),
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
    $request->validate(
      $this->getRules('cancel-payment'),
      $this->getCustomMessage(),
      $this->getAttr()
    );

    $ok = false;
    $message = null;
    $error = null;

    //Recupero la factura
    $invoice = Invoice::without('item')->find($request->invoiceId);
    //Se recupera el pago
    $payment = InvoicePayment::find($request->paymentId);

    if (!$payment->cancel && !$invoice->cancel && $invoice->customer_id) {
      //El saldo de la factura aumenta en el valor del pago
      $invoice->balance = bcadd($invoice->balance, $payment->amount);

      //Si es un pago inicial tambien se actualiza cash(-) y credit(+)
      if ($payment->initial_payment) {
        $invoice->cash = bcsub($invoice->cash, $payment->amount);
        $invoice->credit = bcadd($invoice->credit, $payment->amount);

        if (bccomp($invoice->cash, '0', 2) <= 0) $invoice->cash = null;
      }

      //Se cancela el pago
      $payment->cancel = true;
      $payment->cancel_message = auth()->user()->name . ": " . $request->input('message');

      DB::beginTransaction();

      try {
        //Se elimina la transacción asociada.
        if ($payment->transaction) $payment->transaction->delete();

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
      $message = !$invoice->customer_id
        ? "Cancelar pagos a ventas por mostrador no está soportado."
        : "¡El pago ya fue cancelado.!";
    }

    return [
      'ok' => $ok,
      'invoice' => $invoice,
      'message' => $message,
      'error' => $error
    ];
  }

  public function cancelItem(Request $request)
  {
    $ok = false;
    $message = null;
    $error = null;
    $log = [];

    $request->validate(
      $this->getRules('cancel-item'),
      $this->getCustomMessage(),
      $this->getAttr()
    );

    $log[] = "Los datoś pasan la validación";

    //Se recupera las instancias
    /** @var Invoice */
    $invoice = Invoice::without('items')->find($request->invoiceId);
    /** @var InvoiceItem */
    $item = InvoiceItem::find($request->itemId);

    $log[] = "Se obtinen los recursos de la factura y el articulo";

    if ($item->cancel) {
      return [
        'ok' => false,
        'message' => 'El articulo ya fue cancelado.'
      ];
    }

    //Se calculas las variables del proceso
    /** 
     * Valor Neto del articulo 
     * @var string
     * */
    $itemPrice = bcmul($item->unit_value, $request->quantity, 2);
    /** 
     * Valor total de los descuentos aplicados al articulo 
     * @var string 
     * */
    $discount = $item->discount ? bcmul($item->discount, $request->quantity, 2) : "0";
    /**
     * Valor total a descontar de las facturas
     * @var string
     */
    $amount = bcsub($itemPrice, $discount, 2);
    /**
     * Valor total a descontar de los pagos
     * @var string
     */
    $cashDiscount = "0";

    //Se descuenta el valor neto del articulo
    $invoice->subtotal = bcsub($invoice->subtotal, $itemPrice);
    //Se disminuye el descuento si el articulo lo tiene.
    $invoice->discount = $item->discount
      ? bcsub($invoice->discount, $discount)
      : $invoice->discount;
    //Se diminuye el valor de la factura.
    $invoice->amount = bcsub($invoice->amount, $amount);

    if ($invoice->credit && bccomp($invoice->credit, $amount) > 0) {
      //Se decuenta el valor del articulo
      $invoice->credit = bcsub($invoice->credit, $amount);
    } else {
      //Se anula el credito
      $invoice->credit = null;
    }

    // Se actualiza el estado efectivo de la factura
    if ($invoice->balance) {
      if (bccomp($invoice->balance, $amount) >= 0) {
        $invoice->balance = bcsub($invoice->balance, $amount);
      } else {
        $cashDiscount = bcsub($amount, $invoice->balance);
        $invoice->balance = null;
      }
    } else {
      $cashDiscount = $amount;
    }

    $log[] = "Se realiza la actualización de la factura.";

    //Se procede a hacer la modificaciones
    DB::beginTransaction();
    try {
      //* Se anula el articulo actual y se crea uno nuevo con el remanente.
      $item->cancel = true;
      $item->cancel_message = $request->message;
      $itemQuantity = floatval($item->quantity);
      $cancelQuantity = floatval($request->quantity);
      if ($cancelQuantity <= $itemQuantity) {
        $quantityDiff = floatval($item->quantity) - floatval($request->quantity);

        if (abs($quantityDiff) > 0.0009) {
          //Se crea un nuevo articulo conservando los datos del original
          $newItem = new InvoiceItem([
            'quantity' => $quantityDiff,
            'description' => $item->description,
            'unit_value' => $item->unit_value,
            'discount' => $item->discount,
            'amount' => bcmul(bcsub($item->unit_value, $item->discount, 2), $quantityDiff, 2),
          ]);

          $invoice->items()->save($newItem);
          $log[] = "Se crea un articulo adicional";
        }
      } else {
        return [
          'ok' => $ok,
          'message' => "La cantidad a cancelar es mayor que la cantidad del articulo",
          'log' => $log
        ];
      }

      //Se actualizan los pagos
      if (bccomp($cashDiscount, "0", 2) > 0) {

        //Se recupera el saldo total de los pagos.
        $cashAmount = $invoice
          ->payments()
          ->where('cancel', 0)
          ->sum('amount');

        $log[] = "Se recupera el valor de los pagos: " . $cashAmount;

        if (bccomp($cashDiscount, $cashAmount, 2) <= 0) {
          //Se recuperaon los pagos en orden inverso
          $payments = $invoice->payments()
            ->where('cancel', 0)
            ->orderBy('id', 'DESC')
            ->get();

          //Se recorre cada uno para ir actualizando el valor
          $payments->each(function ($payment, $key) use (&$cashDiscount, &$invoice) {
            //Se comprueba que el saldo del pago
            if (bccomp($cashDiscount, $payment->amount) >= 0) {
              //Se cancela el pago
              $payment->cancel = true;
              $payment->cancel_message = "Cancelación de articulo";

              //Se elimina la transacción asociada
              if ($payment->transaction) $payment->transaction->delete();

              //Se actualiza el efectivo de la factura.
              $payment->initial_payment ? $invoice->cash = bcsub($invoice->cash, $payment->amount) : null;

              //se actualiza el saldo
              $cashDiscount = bcsub($cashDiscount, $payment->amount);
            } else {
              //Se decuenta el valor restante
              $payment->amount = bcsub($payment->amount, $cashDiscount);
              $payment->description = $payment->description . " [*]";
              //Se actualiza la transacción asociada
              if ($payment->transaction) {
                $payment->transaction->transaction_date = Carbon::now()->format('Y-m-d H:i');
                $payment->transaction->description = $payment->transaction->description . " [*]";
                $payment->transaction->amount = bcsub($payment->transaction->amount, $cashDiscount);
                $payment->transaction->save();
              }

              //Se actualiza la factura
              $payment->initial_payment ? $invoice->cash = bcsub($invoice->cash, $cashDiscount) : null;

              //Se actualiza el saldo
              $cashDiscount = "0";
            }

            //Se guarda el estado
            $payment->save();

            if (bccomp($cashDiscount, "0", 2) <= 0) {
              return false;
            }
          });
        } else {
          //Existe un error
          $log[] = "El dinero a descontar es menor que saldo de los pagos [descuento: $cashDiscount, pagos: $cashAmount]";
          return [
            'ok' => $ok,
            'message' => "Error al manipular los pagos.",
            'log' => $log
          ];
        }
      }

      //Se auditan las variables 
      $invoice->discount = bccomp($invoice->discount, 0, 2) != 0 ? $invoice->discount : null;
      $invoice->credit = bccomp($invoice->credit, '0', 2) != 0 ? $invoice->credit : null;
      $invoice->cash = bccomp($invoice->cash, '0', 2) != 0 ? $invoice->cash : null;
      $invoice->balance = bccomp($invoice->balance, '0', 2) != 0 ? $invoice->balance : null;

      if (bccomp($invoice->amount, '0') <= 0) {
        $invoice->cancel = true;
        $invoice->cancel_message = "Factura con importe cero.";
      }

      $invoice->save();
      $item->save();

      DB::commit();
      $ok = true;
    } catch (\Throwable $th) {
      $message = "Error al guardar en la base de datos";
      $error = $th->getMessage() . "\n" . $th->getLine();
      //throw $th;
    }

    return [
      'ok' => $ok,
      'message' => $message,
      'error' => $error,
      'invoice' => $invoice->refresh()
    ];
  }

  public function cancelInvoice(Request $request)
  {
    $res = new stdClass;
    $res->ok = false;
    $res->message = null;
    $res->error = null;
    $res->invoice = null;


    $request->validate(
      $this->getRules('cancel-invoice'),
      $this->getCustomMessage(),
      $this->getAttr()
    );

    //Recupero la instancia de la factura
    /** @var Invoice */
    $invoice = Invoice::with('payments', 'items')->find($request->invoiceId);

    if ($invoice->cancel) {
      $res->message = "La factura ya fue cancelada.";
      return $res;
    }

    $invoice->cancel = true;
    $invoice->cancel_message = $request->message;

    DB::beginTransaction();

    $invoice->items->each(function ($item) use ($request) {
      if (!$item->cancel) {
        $item->cancel = true;
        $item->cancel_message = $request->message;
      }
    });

    $invoice->payments->each(function ($payment) use ($request) {
      if (!$payment->cancel) {
        $payment->cancel = true;
        $payment->cancel_message = $request->message;

        if ($payment->transaction) {
          $payment->transaction()->first()->delete();
        }
      }
    });

    $invoice->push();

    DB::commit();

    $res->ok = true;
    $res->invoice = $invoice;

    return $res;
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

  public function printInvoice(Invoice $invoice)
  {
    $config = $this->getInvoiceInformation();
    return Inertia::render('Invoice/PrintInvoice', compact('invoice', 'config'));
  }

  //------------------------------------------------------------------------
  //  GRAPH
  //------------------------------------------------------------------------
  public function getWeeklyReport()
  {
    //Fechas limitantes
    $now = Carbon::now();
    $lastWeek = $now->clone()->subDays(6)->startOfDay();
    $format = "Y-m-d H:i";

    //Se recuperan las facturas activas.
    $invoices = Invoice::orderBy('expedition_date', "ASC")
      ->where('cancel', 0)
      ->where('expedition_date', '>=', $lastWeek->format($format))
      ->where('expedition_date', '<=', $now->format($format))
      ->without('items')
      ->select(['id', 'expedition_date as date', 'amount', 'cash', 'credit', 'cash_change as change'])
      ->get();

    $payments = InvoicePayment::orderBy('payment_date')
      ->where('cancel', 0)
      ->where('payment_date', '>=', $lastWeek->format($format))
      ->where('payment_date', '<=', $now->format($format))
      ->where('initial_payment', 0)
      ->select(['id', 'payment_date as date', 'amount'])
      ->get();

    return [
      'invoices' => $invoices,
      'payments' => $payments,
    ];
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

  /**
   * Construye las reglas utilizadas para la validacion de nuevas facturas 
   * asi como para los procedimientos de cancelación.
   * @param String $type Tipo de formulario a validar [new-invoice, add-payment, cancel-invoice, cancel-payment, cancel-item]
   * @return Array
   */
  protected function getRules($type = "new-invoice")
  {
    /** @var Array */
    $rules = [];

    if ($type === "new-invoice") {
      $rules = [
        'customerId' => 'nullable|integer|exists:customer,id',
        'customerName' => 'nullable|string|min:3|max:90',
        'customerDocument' => 'nullable|string|min:6|max:45',
        'customerAddress' => 'nullable|string|min:3|max:150',
        'customerPhone' => 'nullable|string|min:6|max:20',
        'expeditionDate' => 'required|date',
        'expirationDate' => 'required|string|date|after_or_equal:expeditionDate',
        'invoiceItems' => 'required|array|min:1',
        'invoiceItems.*' => 'array:quantity,description,unitValue,discount,amount',
        'invoiceItems.*.quantity' => 'required|numeric|min:0.001|max:9999.999',
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
    } else if ($type === 'add-payment') {
      $rules = [
        'invoiceId' => 'required|integer|exists:invoice,id',
        'customerId' => 'required|integer|exists:customer,id',
        'invoicePayments.*' => 'array:boxId,boxName,registerTransaction,amount',
        'invoicePayments.*.boxId' => 'required|integer|exists:cashbox,id',
        'invoicePayments.*.boxName' => 'required|string',
        'invoicePayments.*.registerTransaction' => 'required|boolean',
        'invoicePayments.*.amount' => 'required|numeric|min:1|max:99999999.99',
      ];
    } else {
      $rules = [
        'invoiceId' => 'required|integer|exists:invoice,id',
        'message' => 'required|string|min:3',
        'password' => ['required', 'string', 'min:3', function ($attr, $value, $fail) {
          if (!Hash::check($value, auth()->user()->password)) {
            return $fail('La contraseña es incorrecta.');
          }
        }]
      ];

      if ($type === 'cancel-payment') {
        $rules['paymentId'] = 'required|integer|exists:invoice_payment,id';
      } else if ($type === 'cancel-item') {
        $rules['itemId'] = 'required|integer|exists:invoice_item,id';
        $rules['quantity'] = 'required|numeric|min:0.001';
      }
    }

    return $rules;
  }

  protected function getAttr()
  {
    return [
      // Calves
      'customerId' => 'ID cliente',
      'invoiceId' => "ID de factura",
      'paymentId' => 'ID de pago',
      'itemId' => 'ID de item',
      //Parametros de la factura
      'customerName' => 'cliente',
      'customerDocument' => 'nit',
      'customerAddress' => 'direccíon',
      'customerPhone' => 'telefono',
      'expeditionDate' => 'fecha de expedición',
      'expirationDate' => 'fecha de vencimiento',
      'invoiceItems' => 'listado de articulos',
      'invoicePayments' => 'listado de pagos',
      //Parametros para cancelaciónes
      'message' => 'motivo',
      'password' => 'contraseña',
      'quantity' => 'cantidad',

    ];
  }

  protected function getCustomMessage()
  {
    return [
      'invoiceItems.array' => 'Debe ser un listado de items.',
      'password.required' => 'Se requiere su contraseña para continuar.',
      'message.required' => "Se requiere un motivo para realizar la cancelación"
    ];
  }
}
