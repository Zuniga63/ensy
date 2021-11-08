<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $now = Carbon::now();

    DB::table('users')->insert([
      'id' => 1,
      'name' => 'Administrador',
      'email' => 'admin@admin.com',
      'email_verified_at' => $now,
      'password' => Hash::make('admin'),
      'created_at' => $now,
      'updated_at' => $now
    ]);
  }
}
