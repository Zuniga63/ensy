<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressColumnsToBuildingAdmin extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('building_admin', function (Blueprint $table) {
      $table->foreignId('country_department_id')
        ->nullable()
        ->after('id')
        ->constrained('country_department')
        ->nullOnDelete();
      $table->foreignId('town_id')
        ->nullable()
        ->after('country_department_id')
        ->constrained('town')
        ->nullOnDelete();
      $table
        ->foreignId('town_district_id')
        ->nullable()
        ->after('town_id')
        ->constrained('town_district')
        ->nullOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('building_admin', function (Blueprint $table) {
      $table->dropForeign(['country_department_id']);
      $table->dropForeign(['town_id']);
      $table->dropForeign(['town_district_id']);
      $table->dropColumn(['country_department_id', 'town_id', 'town_district_id']);
    });
  }
}
