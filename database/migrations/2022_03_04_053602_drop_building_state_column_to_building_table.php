<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropBuildingStateColumnToBuildingTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('building', function (Blueprint $table) {
      $table->dropColumn('building_state');
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
      $table->string('building_state', 45)->nullable()->after('socioeconomic');
    });
  }
}
