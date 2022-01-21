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
    for ($i=0; $i < 3; $i++) { 
      $name = 'Caja ' . $i +1;
      $slug = 'caja_' . $i +1;

      DB::table('cashbox')->insert([
        'name' => $name,
        'slug' => $slug,
        'order' => $i +2,
        'created_at' => $date,
        'updated_at' => $date,
      ]);
    }

    //Se crean las transacciones de la caja principal
    $incomes = 1e6;
    $balance = 0;
    
    for ($day=1; $day <= 30; $day++) { 
      $incomes += $incomes * (rand(-5, 5) / 100);
      $expeses = $incomes * (rand(80, 120) / 100.0);
      
      $incomes = floor($incomes / 100) * 100;
      $expeses = floor($expeses / 100) * 100;
      
      if($balance + $incomes - $expeses < 0){
        $expeses = $balance + $incomes;
      }

      $balance += $incomes - $expeses;

      $incomeDrescription = "Ingresos del día " . $day;
      $expenseDescription = "Gastos del día " . $day;

      DB::table('cashbox_transaction')->insert([
        'cashbox_id' => 1,
        'transaction_date' => $date,
        'description' => $incomeDrescription,
        'amount' => $incomes,
        'transfer' => false,
        'blocked' => false,
        'created_at' => $date,
        'updated_at' => $date
      ]);
      
      DB::table('cashbox_transaction')->insert([
        'cashbox_id' => 1,
        'transaction_date' => $date,
        'description' => $expenseDescription,
        'amount' => $expeses * -1,
        'transfer' => false,
        'blocked' => false,
        'created_at' => $date,
        'updated_at' => $date
      ]);

      $date->addDay();
    }
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
