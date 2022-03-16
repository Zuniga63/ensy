<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMandateContractColumnToBuilding extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('building', function (Blueprint $table) {
      $table->boolean('mandate_contract')->after('available')->default(false);
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
      $table->dropColumn('mandate_contract');
    });
  }
}
