<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankTransactionTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('bank_transaction', function (Blueprint $table) {
      $table->id();
      $table->foreignId('bank_id')->nullable()->constrained('bank')->nullOnDelete();
      $table->foreignId('bank_account_id')->nullable()->constrained('bank_account')->nullOnDelete();
      $table->dateTime('transaction_date');
      $table->string('description');
      $table->decimal('amount', 10, 2);
      $table->string('code')->nullable();
      $table->boolean('transfer');
      $table->boolean('blocked');
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
    Schema::dropIfExists('bank_transaction');
  }
}
