<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePaymentTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('invoice_payment', function (Blueprint $table) {
      $table->id();
      $table->foreignId('invoice_id')->constrained('invoice');
      $table->foreignId('customer_id')->nullable()->constrained('customer')->nullOnDelete();
      $table->foreignId('transaction_id')->nullable()->constrained('cashbox_transaction')->nullOnDelete();
      $table->timestamp('payment_date')->useCurrent();
      $table->string('description', 150)->nullable();
      $table->decimal('amount', 10, 2);                   //{0.00 - 99'999'999'.99}
      $table->boolean('initial_payment')->default(false);
      $table->boolean('cancel')->default(0);
      $table->string('cancel_message')->nullable();
      $table->boolean('locked')->default(false);
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
    Schema::dropIfExists('invoice_payment');
  }
}
