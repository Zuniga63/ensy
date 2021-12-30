<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashboxTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('cashbox', function (Blueprint $table) {
      $table->id();
      $table->string('name', 50)->unique();
      $table->string('slug', 50)->unique();
      $table->string('code', 20)->nullable()->unique();
      $table->unsignedTinyInteger('order')->default(1);
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
    Schema::dropIfExists('cashbox');
  }
}
