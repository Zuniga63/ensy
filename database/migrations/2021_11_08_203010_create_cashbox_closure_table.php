<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashboxClosureTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('cashbox_closure', function (Blueprint $table) {
      $table->id();
      $table->foreignId('cashbox_id')->constrained('cashbox')->cascadeOnDelete();
      $table->dateTime('closing_date');
      $table->decimal('base', 8, 0);
      $table->decimal('new_base', 8, 0);
      $table->unsignedDecimal('incomes', 11, 2)->nullable();
      $table->unsignedDecimal('expenses', 11, 2)->nullable();
      $table->json('coins')->nullable();
      $table->json('bills')->nullable();
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
    Schema::dropIfExists('cashbox_closure');
  }
}
