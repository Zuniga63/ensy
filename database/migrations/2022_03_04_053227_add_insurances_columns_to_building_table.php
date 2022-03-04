<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInsurancesColumnsToBuildingTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('building', function (Blueprint $table) {
      $table->string('insurance_carrier', 45)->nullable()->after('comission');
      $table->string('insurance_type', 45)->nullable()->after('insurance_carrier');
      $table->float('insurance_comission', 3, 2)->default(0.00)->after('insurance_type');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('building', function (Blueprint $table) {
      $table->dropColumn(['insurance_carrier', 'insurance_type', 'insurance_comission']);
    });
  }
}
