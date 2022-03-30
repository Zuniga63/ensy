<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDailyActivityTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('daily_activity', function (Blueprint $table) {
      $table->id();
      $table->timestamp('start_date')->default(DB::raw('CURRENT_TIMESTAMP'));
      $table->timestamp('end_date')->default(DB::raw('CURRENT_TIMESTAMP'));
      $table->string('title', 45);
      $table->string('description')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('daily_activity');
  }
}
