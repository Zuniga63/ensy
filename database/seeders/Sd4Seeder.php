<?php

namespace Database\Seeders;

use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceItem;
use App\Models\Invoice\InvoicePayment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class Sd4Seeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $tables = ['invoice_payment', 'invoice_item', 'invoice', 'cashbox_transaction', 'cashbox'];
    $this->truncateTables($tables);

    //Se crea la caja principal
    $box = Cashbox::create([
      'name' => 'Caja Principal',
      'slug' => 'caja_principal',
      'order' => 1
    ]);

    $this->createSales($box);
    $this->createOtherTransactions($box);
  }

  /**
   * Este metodo se encarga de eliminar los datos
   * de las tablas con el fin de que cuando se 
   * planten los seeder esto evite crear duplicados.
   */
  protected function truncateTables($tables)
  {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    foreach ($tables as $table) {
      DB::table($table)->truncate();
    } //end foreach
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
  } //end function

  /**
   * @param  \App\Models\Cashbox  $box
   */
  protected function createSales($box)
  {
    //Se registran las ventas
    $saleTransactions = DB::connection('carmu')
      ->table('box_transaction')
      ->orderBy('transaction_date', 'ASC')
      ->where('type', 'sale')
      ->whereIn('box_id', [3, 4, 7])
      ->get();

    //Necesito organizar las ventas por días
    $saleIndex = 0;
    $dateFormat = "Y-m-d H:i:s";
    $date = Carbon::createFromFormat($dateFormat, $saleTransactions[$saleIndex]->transaction_date)->midDay();
    $now = Carbon::now();
    /** @var Collection */
    $dailySales = new Collection();

    while ($date->lessThanOrEqualTo($now)) {
      $startDay = $date->copy()->startOfDay();
      $endDay = $startDay->copy()->endOfDay();
      $transactions = new Collection();
      $count = 0;

      for ($index = $saleIndex; $index < $saleTransactions->count(); $index++) {
        $transactionDate = Carbon::createFromFormat($dateFormat, $saleTransactions[$index]->transaction_date);
        if ($transactionDate->greaterThanOrEqualTo($startDay) && $transactionDate->lessThanOrEqualTo($endDay)) {
          $transactions->push($saleTransactions[$index]);
          $count++;
          $saleIndex++;
        } else {
          break;
        }
      }

      $daily = new stdClass;
      $daily->date = $date->copy();
      $daily->count = $count;
      $daily->transactions = $transactions;

      $dailySales->push($daily);

      $date->addDay();
    }

    //En este punto se tienen las ventas agrupadas por días y se procede
    //a crear las facturas.
    DB::beginTransaction();
    $invoiceNumber = 1;
    $dailySales->each(function ($daily) use (&$invoiceNumber, $box) {
      if ($daily->count > 0) {
        $amount = 0;
        $daily->transactions->each(function ($tran) use (&$amount) {
          $amount = bcadd($amount, $tran->amount);
        });

        $invoice = new Invoice([
          'seller_id' => 1,
          'number' => $invoiceNumber,
          'seller_name' => "Administrador",
          'expedition_date' => $daily->date->format('Y-m-d H:i'),
          'expiration_date' => $daily->date->format('Y-m-d H:i'),
          'amount' => $amount,
          'cash' => $amount,
        ]);

        $item = new InvoiceItem([
          'quantity' => 1,
          'description' => 'Articulos por mostrador',
          'unit_value' => $amount,
          'amount' => $amount
        ]);

        $payment = new InvoicePayment([
          'payment_date' => $daily->date->format('Y-m-d H:i'),
          'description' => "Deposito en $box->name",
          'amount' => $amount,
          'initial_payment' => true
        ]);

        $transaction = new CashboxTransaction([
          'cashbox_id' => $box->id,
          'transaction_date' => $daily->date->format('Y-m-d H:i'),
          'description' => "Venta: Mostrador",
          'amount' => $amount,
          'blocked' => true,
        ]);

        if ($transaction->save()) {
          $payment->transaction()->associate($transaction);
        }

        if ($invoice->save()) {
          $invoice->items()->save($item);
          $invoice->payments()->save($payment);
        }

        $invoiceNumber++;
      } //.end each
    });

    DB::commit();
  }

  /**
   * @param  \App\Models\Cashbox  $box
   */
  protected function createOtherTransactions($box)
  {
    $data = DB::connection('carmu')
      ->table('box_transaction')
      ->orderBy('transaction_date', 'ASC')
      ->whereNotIn('type', ['sale', 'transfer'])
      ->whereIn('box_id', [3, 4, 7])
      ->get();

    $transactions = [];

    $data->each(function ($item) use (&$transactions) {
      $description = "General: ";
      if ($item->type === 'expense') $description = "Gasto: ";
      else if ($item->type === 'purchase') $description = "Compra: ";
      else if ($item->type === 'service') $description = "Servicio: ";
      else if ($item->type === 'credit') $description = "Credito: ";
      else if ($item->type === 'payment') $description = "Abono: ";

      $description .= $item->description;
      $transactions[] = new CashboxTransaction([
        'transaction_date' => $item->transaction_date,
        'description' => $description,
        'amount' => $item->amount,
      ]);
    });

    $box->transactions()->saveMany($transactions);
  }
}
