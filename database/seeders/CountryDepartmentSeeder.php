<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountryDepartmentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      ['id' => 1, 'name' => "Antioquia", 'code' => 5],
      ['id' => 2, 'name' => "Atlantico", 'code' => 8],
      ['id' => 3, 'name' => "D. C. Santa Fe de Bogotá", 'code' => 11],
      ['id' => 4, 'name' => "Bolivar", 'code' => 13],
      ['id' => 5, 'name' => "Boyaca", 'code' => 15],
      ['id' => 6, 'name' => "Caldas", 'code' => 17],
      ['id' => 7, 'name' => "Caqueta", 'code' => 18],
      ['id' => 8, 'name' => "Cauca", 'code' => 19],
      ['id' => 9, 'name' => "Cesar", 'code' => 20],
      ['id' => 10, 'name' => "Cordova", 'code' => 23],
      ['id' => 11, 'name' => "Cundinamarca", 'code' => 25],
      ['id' => 12, 'name' => "Choco", 'code' => 27],
      ['id' => 13, 'name' => "Huila", 'code' => 41],
      ['id' => 14, 'name' => "La Guajira", 'code' => 44],
      ['id' => 15, 'name' => "Magdalena", 'code' => 47],
      ['id' => 16, 'name' => "Meta", 'code' => 50],
      ['id' => 17, 'name' => "Nariño", 'code' => 52],
      ['id' => 18, 'name' => "Norte de Santander", 'code' => 54],
      ['id' => 19, 'name' => "Quindio", 'code' => 63],
      ['id' => 20, 'name' => "Risaralda", 'code' => 66],
      ['id' => 21, 'name' => "Santander", 'code' => 68],
      ['id' => 22, 'name' => "Sucre", 'code' => 70],
      ['id' => 23, 'name' => "Tolima", 'code' => 73],
      ['id' => 24, 'name' => "Valle", 'code' => 76],
      ['id' => 25, 'name' => "Arauca", 'code' => 81],
      ['id' => 26, 'name' => "Casanare", 'code' => 85],
      ['id' => 27, 'name' => "Putumayo", 'code' => 86],
      ['id' => 28, 'name' => "San Andres", 'code' => 88],
      ['id' => 29, 'name' => "Amazonas", 'code' => 91],
      ['id' => 30, 'name' => "Guainia", 'code' => 94],
      ['id' => 31, 'name' => "Guaviare", 'code' => 95],
      ['id' => 32, 'name' => "Vaupes", 'code' => 97],
      ['id' => 33, 'name' => "Vichada", 'code' => 99],
    ];

    foreach ($data as $index => $item) {
      $data[$index]['created_at'] = Carbon::now();
      $data[$index]['updated_at'] = Carbon::now();
    }

    DB::table('country_department')->insert($data);
  }
}
