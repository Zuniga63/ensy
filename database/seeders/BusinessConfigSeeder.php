<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessConfigSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $now = Carbon::now();
    DB::table('business_config')->insert([
      'id' => 1,
      'created_at' => $now,
      'updated_at' => $now
    ]);
  }
}
