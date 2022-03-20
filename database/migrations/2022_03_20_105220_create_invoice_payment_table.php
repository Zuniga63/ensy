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
      $table->foreignId('customer_id')->nullable()->constrained('customer')->nullOnDelete();
      $table->timestamp('payment_date');
      $table->decimal('amount', 10, 2);
      $table->boolean('cancel')->default(0);
      $table->string('cancel_message')->nullable();
      $table->string('transaction_code')->nullable();
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
