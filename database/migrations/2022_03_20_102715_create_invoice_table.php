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
      $table->string('prefix', 10)->nullable();                 //{0-10}
      $table->unsignedInteger('number');                        //{0-4.294.967.295}
      $table->string('customer_name', 90)                            //{0-90}
        ->default('Mostrador');
      $table->string('customer_document', 45)->nullable();      //{0-45}
      $table->string('customer_address', 150)->nullable();      //{0-150}
      $table->string('customer_phone', 20)->nullable();
      $table->string('seller_name', 90)->nullable();
      $table->timestamp('expedition_date')->useCurrent();
      $table->timestamp('expiration_date')->useCurrent();
      $table->decimal('subtotal', 10, 2)->nullable();           //{0.00 - 99'999'999'.99}
      $table->decimal('discount', 10, 2)->nullable();           //{0.00 - 99'999'999'.99}
      $table->decimal('amount', 10, 2);                         //{0.00 - 99'999'999'.99}
      $table->decimal('cash', 10, 2)->nullable();               //{0.00 - 99'999'999'.99}
      $table->decimal('credit', 10, 2)->nullable();             //{0.00 - 99'999'999'.99}
      $table->decimal('cash_change', 10, 2)->nullable();        //{0.00 - 99'999'999'.99}
      $table->decimal('balance', 10, 2)->nullable();            //{0.00 - 99'999'999'.99}
      $table->boolean('cancel')->default(0);
      $table->string('cancel_message')->nullable();
      $table->boolean('locked')->default(false);
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
