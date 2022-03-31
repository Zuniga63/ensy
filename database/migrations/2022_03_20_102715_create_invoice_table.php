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
      $table->string('prefix', 10)->nullable();
      $table->unsignedInteger('number');
      $table->string('customer', 90);
      $table->string('customer_document', 90);
      $table->string('customer_address', 150)->nullable();
      $table->string('customer_phone')->nullable();
      $table->string('seller', 90)->nullable();
      $table->timestamp('expedition_date')->useCurrent();
      $table->timestamp('expiration_date')->useCurrent();
      $table->decimal('subtotal', 10,2)->nullable();
      $table->decimal('discount', 10,2)->nullable();
      $table->decimal('amount', 10, 2);
      $table->decimal('cash', 10, 2)->nullable();
      $table->decimal('credit', 10, 2)->nullable();
      $table->decimal('balance', 10, 2)->nullable();
      $table->boolean('cancel')->default(0);
      $table->string('cancel_message')->nullable();
      $table->unique(['prefix', 'number'], 'unique_invoice');
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
