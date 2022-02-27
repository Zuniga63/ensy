<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CashboxSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $date = Carbon::now()->subMonth();
    $this->truncateTables(['cashbox', 'cashbox_transaction']);

    //Se crea la caja principal
    DB::table('cashbox')->insert([
      'id' => 1,
      'name' => 'Main Box',
      'slug' => 'main_box',
      'code' => '110505001',
      'created_at' => $date,
      'updated_at' => $date,
    ]);

    //Se crean 10 cajas genericas
    for ($i = 0; $i < 3; $i++) {
      $name = 'Caja ' . $i + 1;
      $slug = 'caja_' . $i + 1;

      DB::table('cashbox')->insert([
        'name' => $name,
        'slug' => $slug,
        'order' => $i + 2,
        'created_at' => $date,
        'updated_at' => $date,
      ]);
    }

    //Se crean las transacciones de la caja principal
    $base = 2e6;                //La base con la que se empieza
    $breakfastCost = -2.5e3;       //Costo del desayuni
    $lunchCost = -9e3;           //Costo del almuerzo
    $dinnerCost = -2.5e3;          //Costo de la cena

    $housingCost = -350e3;       //Costo mensual de vivienda
    $costOfService = -50e3;     //Costos de los servicios
    $monthlyIncome = 1200e3;    //Ingresos mensuales
    $transportCost = -4e3;       //costos de transporte de lunes a viernes
    $gymCost = -80e3;            //Costo del gimnasio
    $clothesCost = -300e3;       //Gasto en ropa cada tres meses

    $now = Carbon::now();                         //Criterio de parada
    $date = Carbon::now()                         //Variable incrementable
      ->subYears(4)
      ->startOfYear()
      ->addHours(8);
    $months = 1;                                  //Para llevar la cuenta de los meses.
    $endOfMonth = $date->copy()->endOfMonth();
    $transactions = [];

    //Se resgistra la base y los pagos mensuales
    $transactions[] = $this->addTransaction($date, "Base", $base);
    $transactions[] = $this->addTransaction($date, "Arriendo", $housingCost);
    $transactions[] = $this->addTransaction($date, "Mensualidad del gimnasio", $gymCost);

    while ($date->lessThanOrEqualTo($now)) {
      //En este punto son las ocho de la mañana
      $transactions[] = $this->addTransaction($date, "Comida: Desayuno", $breakfastCost);

      //Se agrega el costo de transporte si es dia laboral
      if ($date->isWeekday()) {
        $transactions[] = $this->addTransaction($date, "Transporte", $transportCost);
      }

      //Se mueve la fecha a medio dia
      $date->addHours(4);
      $transactions[] = $this->addTransaction($date, "Comida: Almuerzo", $lunchCost);

      //Se mueve la fecha a las dos de latarde
      $date->addHours(2);

      //Si la fecha es mayor que la fecha de fin de mes
      if ($date->greaterThan($endOfMonth)) {
        //Se define la nueva fecha del fin de mes
        $endOfMonth = $date->copy()->endOfMonth();

        //Se cobra el sueldo y pagan los servicios
        $transactions[] = $this->addTransaction($date, "Sueldo", $monthlyIncome);
        $transactions[] = $this->addTransaction($date, "Arriendo", $housingCost);
        $transactions[] = $this->addTransaction($date, "Servicios publicos y privados", $costOfService);
        $transactions[] = $this->addTransaction($date, "Mensualidad de gimnasio", $gymCost, 2);

        //Se hace el ahorro mensual
        $transactions[] = $this->addTransaction($date, "Movido a ahorro personal", $monthlyIncome * 0.1 * -1, 1, true);
        $transactions[] = $this->addTransaction($date, "Deposito en efectivo", $monthlyIncome * 0.1, 2, true);

        //Gastos de vestimenta
        if ($months % 3 === 0) {
          $transactions[] = $this->addTransaction($date, "Compra de ropa", $clothesCost);
        }

        //Se aumenta el contador del mes
        $months++;
      }

      //Se mueve la hora a las cinco de la tarde
      $date->addHours(3);

      if ($date->isWeekday()) {
        $transactions[] = $this->addTransaction($date, "Transporte", $transportCost);
      }

      //Se mueve la hora a las siete de la noche
      $date->addHours(2);
      $transactions[] = $this->addTransaction($date, "Comida: Cena", $dinnerCost);

      //Se mueve la fecha al siguiente día a las ocho
      $date->addDay()->startOfDay()->addHours(8);
    }

    DB::table('cashbox_transaction')->insert($transactions);
  }

  /**
   * Registra una nueva transacción en la base de datos
   * @param Carbon $date La fecha de la transacción
   * @param string $description 
   * @param float $amount Importe de la transacción
   * @param int $boxId El identificador de la caja
   */
  protected function addTransaction($date, $description, $amount, $boxId = 1, $isATransfer = false)
  {
    return [
      'cashbox_id' => $boxId,
      'transaction_date' => $date->format('Y-m-d H:i:s'),
      'description' => $description,
      'amount' => $amount,
      'transfer' => $isATransfer,
      'blocked' => false,
      'created_at' => $date->format('Y-m-d H:i:s'),
      'updated_at' => $date->format('Y-m-d H:i:s')
    ];
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
}
