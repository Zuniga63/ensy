<?php

namespace Database\Seeders;

use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Models\Customer\Customer;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceItem;
use App\Models\Invoice\InvoicePayment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class CarmuSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->command->info('Se inician operaciones preliminares.');
    $this->truncateTables();

    $this->command->info("Se inicia la inserción de los clientes.");
    $this->createCustomers();
    #$this->command->newLine(2);

    #$this->command->info("Se inicia la inserción de las ventas por mostrador.");
    #$this->addInvoices();
    #$this->command->newLine(2);

    #$this->command->info("Se agregan las otras transacciones");
    #$this->addOtherTransactions();
  }


  /**
   * Este metodo se encarga de eliminar los datos
   * de las tablas con el fin de que cuando se 
   * planten los seeder esto evite crear duplicados.
   */
  protected function truncateTables()
  {
    $tables = [
      'cashbox',
      'cashbox_transaction',
      'customer', 'invoice',
      'invoice_item',
      'invoice_payment',
      'product',
      'product_category',
      'product_has_category'
    ];

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    foreach ($tables as $table) {
      DB::table($table)->truncate();
    } //end foreach
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    //Se crea la caja principal
    Cashbox::create([
      'id' => 1,
      'name' => "Caja Principal",
      'slug' => 'caja_principal',
    ]);

    //Se crean las categorías principales
    DB::table('product_category')->insert([
      ['id' => 5, 'name' => 'Morral', 'slug' => 'morral', 'level' => 1],
      ['id' => 4, 'name' => 'Relojería', 'slug' => 'relojeria', 'level' => 1],
      ['id' => 2, 'name' => 'Ropa', 'slug' => 'ropa', 'level' => 1],
      ['id' => 3, 'name' => 'Accesorio', 'slug' => 'accesorio', 'level' => 1],
      ['id' => 7, 'name' => 'Zapatos', 'slug' => 'zapatos', 'level' => 1],
      ['id' => 1, 'name' => 'Joyería', 'slug' => 'joyeria', 'level' => 1],
    ]);

    DB::table('product')->insert([
      ['id' => 5, 'name' => 'Morral', 'slug' => 'morral'],
      ['id' => 4, 'name' => 'Relojería', 'slug' => 'relojeria'],
      ['id' => 2, 'name' => 'Ropa', 'slug' => 'ropa'],
      ['id' => 3, 'name' => 'Accesorio', 'slug' => 'accesorio'],
      ['id' => 7, 'name' => 'Zapatos', 'slug' => 'zapatos'],
      ['id' => 1, 'name' => 'Joyería', 'slug' => 'joyeria'],
    ]);

    DB::table('product_has_category')->insert([
      ['product_id' => 5, 'product_category_id' => 5],
      ['product_id' => 4, 'product_category_id' => 4],
      ['product_id' => 2, 'product_category_id' => 2],
      ['product_id' => 3, 'product_category_id' => 3],
      ['product_id' => 7, 'product_category_id' => 7],
      ['product_id' => 1, 'product_category_id' => 1],
    ]);
  } //end function

  protected function createCustomers()
  {
    //Se recuepera el listado de clientes
    $customerList = DB::connection('carmu_old')
      ->table('customer')
      ->orderBy('first_name')
      ->orderBy('last_name')
      ->where('customer_id', 49)
      #->limit(20)
      ->get();

    /** @var int */
    $this->command->getOutput()->progressStart($customerList->count());
    $customerList->each(function ($customerData) {
      $this->command->getOutput()->progressAdvance();
      $this->registerCustomer($customerData);
    });
    $this->command->getOutput()->progressFinish();
  }

  /**
   * Se encarga de registrar un cliente de la vieja base de datos en 
   * la nueva base de datos.
   * @param mixed $customerData
   */
  protected function registerCustomer($customerData)
  {

    /** @var Customer */
    $customer = new Customer([
      'first_name' => $customerData->first_name,
      'last_name' => $customerData->last_name,
      'document_number' => $customerData->nit,
      'document_type' => 'CC',
    ]);

    if ($customer->save()) {
      //Se recuperan los creditos del cliente
      $creditList = DB::connection('carmu_old')
        ->table('customer_credit')
        ->where('customer_id', $customerData->customer_id)
        ->orderBy('credit_date')
        ->get();

      $creditList = $this->groupCreditPerDay($creditList);

      //Se recuperan los abonos del cliente
      $paymentList = DB::connection('carmu_old')
        ->table('customer_payment')
        ->where('customer_id', $customerData->customer_id)
        ->orderBy('payment_date')
        ->get();

      $invoices = $this->registerCustomerInvoices($customer, $creditList);
    }
  }

  /**
   * Utiliza el listado de creditos del cliente y va creando las facturas sin
   * saldar.
   * @param Customer $customer modelo del cliente
   * @param Illuminate\Support\Collection $creditsPerDay
   */
  protected function registerCustomerInvoices($customer, $creditsPerDay)
  {
    $invoices = new Collection();
    $invoiceNumber = Invoice::max('id') + 1;

    foreach ($creditsPerDay as $dailyCredits) {
      $date = $dailyCredits->date;
      $credits = $dailyCredits->credits;
      $amount = "0";
      $items = [];


      //En primer lugar se crean los items
      foreach ($credits as $credit) {
        $items[] = new InvoiceItem([
          'quantity' => 1,
          'description' => $credit->description,
          'unit_value' => $credit->amount,
          'amount' => $credit->amount
        ]);

        $amount = bcadd($amount, $credit->amount);
      }

      //Se crea la factura y se guarda
      $invoice = new Invoice([
        'seller_id' => 1,
        'number' => $invoiceNumber,
        'customer_name' => $customer->full_name,
        'customer_document' => $customer->document_number,
        'seller_name' => 'Administrador',
        'expedition_date' => $date->format('Y-m-d H:i'),
        'expiration_date' => $date->format('Y-m-d H:i'),
        'amount' => $amount,
        'credit' => $amount,
        'balance' => $amount,
      ]);

      if ($customer->invoices()->save($invoice)) {
        $invoice->items()->saveMany($items);
        $invoices->push($invoice);
        $invoiceNumber++;
      }
    }


    return $invoices;
  }

  protected function registerCustomerPayments($invoices, $paymentList)
  {
    //TODO
  }

  /**
   * Se encarga de agrupar los creditos por días
   * ? Este metodo es util porque me permite
   * ? poder agrupara varios creditos en una sola factura.
   * @param Illuminate\Support\Collection $list listado a agrupar.
   */
  protected function groupCreditPerDay($creditList)
  {
    $creditsPerDay = new Collection();    //Creditos agrupados por día de creación
    $format = "Y-m-d H:i:s";              //Formato para crear las instancias Carbon a partir de los creditos

    if ($creditList->count() > 0) {
      $firstCredit = $creditList[0];
      $lastCredit = $creditList[$creditList->count() - 1];

      $date = Carbon::createFromFormat($format, $firstCredit->credit_date)->midDay();
      $endDate = Carbon::createFromFormat($format, $lastCredit->credit_date)->endOfDay();
      $lastIndex = 0;   //Para recordar donde debe iniciar el for

      while ($date->lessThanOrEqualTo($endDate)) {
        $startDay = $date->copy()->startOfDay();
        $endDay = $date->copy()->endOfDay();

        $credits = new Collection();

        for ($index = $lastIndex; $index < $creditList->count(); $index++) {
          $credit = $creditList[$index];
          $creditDate = Carbon::createFromFormat($format, $credit->credit_date);

          if ($creditDate->greaterThanOrEqualTo($startDay) && $creditDate->lessThanOrEqualTo($endDay)) {
            $credits->push($credit);
            $lastIndex++;
          } else {
            break;
          } //.end if-else
        } // .end for

        if ($credits->count() > 0) {
          $dailyCredits = new stdClass;
          $dailyCredits->date = $date->copy();
          $dailyCredits->credits = $credits;

          $creditsPerDay->push($dailyCredits);
        }

        $date->addDay();
      } //.end while
    } //.end if

    return $creditsPerDay;
  }

  protected function addInvoices()
  {
    $this->command->info("Se recupera la información de las ventas.");
    $data = DB::connection('carmu_old')->table('sale')
      ->orderBy('sale_date')
      ->join('sale_has_category', 'sale.sale_id', '=', 'sale_has_category.sale_id')
      ->join('sale_category', 'sale_category.category_id', '=', 'sale_has_category.category_id')
      ->select('sale.*', 'sale_category.*')
      ->get();

    $invoiceNumber = Invoice::max('number') + 1;
    $dailySales = new Collection();
    $dataIndex = 0;
    $date = Carbon::createFromFormat('Y-m-d H:i:s', $data[0]->sale_date)->midDay();
    $now = Carbon::now();

    $this->command->info("Se agrupan las ventas por días.");
    while ($date->lessThanOrEqualTo($now)) {
      $startDay = $date->copy()->startOfDay();
      $endDay = $date->copy()->endOfDay();
      $daily = new Collection();
      $amount = "0";

      for ($index = $dataIndex; $index < $data->count(); $index++) {
        $dataItem = $data[$index];
        $dataDate = Carbon::createFromFormat('Y-m-d H:i:s', $dataItem->sale_date);

        if ($dataDate->greaterThanOrEqualTo($startDay) && $dataDate->lessThanOrEqualTo($endDay)) {
          $daily->push($dataItem);
          $amount = bcadd($amount, $dataItem->amount);
          $dataIndex++;
        } else {
          break;
        }
      }

      if ($daily->count() > 0) {
        $dailySales->push([
          'date' => $date->copy(),
          'sales' => $daily,
          'amount' => $amount,
        ]);
      }

      $date->addDay();
    }

    $this->command->info("Se crean las facturas.");

    //Ahora se registran las facturas
    $dailySales->each(function ($daily) use (&$invoiceNumber) {
      $date = $daily['date'];
      $amount = $daily['amount'];
      /** @var Collection */
      $sales = $daily['sales'];

      $this->command->info("Factura del día: " . $date->format('Y-m-d'));

      $invoice = new Invoice([
        'seller_id' => 1,
        'number' => $invoiceNumber,
        'customer_name' => "Mostrador",
        'seller_name' => "Adminsitrador",
        'expedition_date' => $date->format('Y-m-d H:i'),
        'expiration_date' => $date->format('Y-m-d H:i'),
        'amount' => $amount,
        'cash' => $amount,
      ]);

      $payment = new InvoicePayment([
        'payment_date' => $date->format('Y-m-d H:i'),
        'description' => "Deposito en caja principal",
        'amount' => $amount
      ]);

      $transaction = new CashboxTransaction([
        'cashbox_id' => 1,
        'transaction_date' => $date->format('Y-m-d H:i'),
        'description' => "Pago de la factura N° $invoiceNumber",
        'amount' => $amount,
        'blocked' => true,
      ]);

      $items = [];

      $transaction->save();
      $invoice->save();

      $payment->transaction()->addConstraints($transaction);
      $invoice->payments()->save($payment);

      $sales->each(function ($sale) use (&$items) {
        $item = new InvoiceItem([
          'product_id' => null,
          'quantity' => 1,
          'description' => $sale->description,
          'unit_value' => $sale->amount,
          'amount' => $sale->amount,
        ]);

        if ($sale->category_id != 6) {
          $item->product_id = $sale->category_id;
        }

        $items[] = $item;
      });

      $invoice->items()->saveMany($items);

      $invoiceNumber++;
    });

    $this->command->info("Finaliza el proceso de agregar ventas por mostrador.");
  }

  protected function addOtherTransactions()
  {
    $data = DB::connection('carmu')
      ->table('box_transaction')
      ->orderBy('transaction_date', 'ASC')
      ->whereNotIn('type', ['sale', 'transfer', 'payment', 'credit'])
      ->whereIn('box_id', [1, 2])
      ->get();

    $box = Cashbox::find(1);

    $transactions = [];

    $data->each(function ($item) use (&$transactions, $box) {
      $description = "General: ";
      if ($item->type === 'expense') $description = "Gasto: ";
      else if ($item->type === 'purchase') $description = "Compra: ";
      else if ($item->type === 'service') $description = "Servicio: ";

      $description .= $item->description;
      $transactions[] = [
        'cashbox_id' => $box->id,
        'transaction_date' => $item->transaction_date,
        'description' => $description,
        'amount' => $item->amount,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ];
    });

    CashboxTransaction::insert($transactions);
  }
}
