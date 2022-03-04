<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAntiquityColumnsToBuildingTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('building', function (Blueprint $table) {
      $table->dropColumn('antiquity');
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
      $table->timestamp('antiquity')->nullable()->after('floor');
    });
  }
}
