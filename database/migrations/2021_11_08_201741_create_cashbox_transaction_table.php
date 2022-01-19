<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashboxTransactionTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('cashbox_transaction', function (Blueprint $table) {
      $table->id();
      $table->foreignId('cashbox_id')->nullable()->constrained('cashbox')->nullOnDelete();
      $table->dateTime('transaction_date');
      $table->string('description');
      $table->decimal('amount', 10, 2);
      $table->string('code')->nullable();
      $table->boolean('transfer')->default(0);
      $table->boolean('blocked')->default(0);
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
    Schema::dropIfExists('cashbox_transaction');
  }
}
