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
      $table->string('name', 50);
      $table->string('slug', 50);
      $table->string('code', 20)->nullable();
      $table->unsignedTinyInteger('order')->default(1);
      $table->decimal('base', 8, 0)->default(0);
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
