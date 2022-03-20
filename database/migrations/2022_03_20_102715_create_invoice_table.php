<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('invoice', function (Blueprint $table) {
      $table->id();
      $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
      $table->foreignId('customer_id')->nullable()->constrained('customer')->nullOnDelete();
      $table->string('customer', 90)->nullable();
      $table->string('customer_document', 90)->nullable();
      $table->string('seller', 90)->nullable();
      $table->string('invoice_prefix', 45)->nullable();
      $table->unsignedInteger('invoice_number');
      $table->timestamp('invoice_date');
      $table->decimal('amount', 10, 2);
      $table->decimal('cash', 10, 2)->nullable();
      $table->decimal('credit', 10, 2)->nullable();
      $table->decimal('balance', 10, 2)->nullable();
      $table->boolean('cancel')->default(0);
      $table->string('cancel_message')->nullable();
      $table->string('transaction_code')->nullable();
      $table->unique(['invoice_prefix', 'invoice_number'], 'unique_invoice');
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
    Schema::dropIfExists('invoice');
  }
}
