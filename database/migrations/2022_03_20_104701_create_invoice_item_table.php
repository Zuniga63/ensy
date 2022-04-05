<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('invoice_item', function (Blueprint $table) {
      $table->id();
      $table->foreignId('invoice_id')->constrained('invoice');
      $table->unsignedFloat('quantity', 8, 4);              //{0.0000 - 9'999.9999}
      $table->string('description');                        //{0-255}
      $table->decimal('unit_value', 10, 2);                 //{0.00 - 99'999'999'.99}
      $table->decimal('discount', 10, 2)->nullable();       //{0.00 - 99'999'999'.99}
      $table->decimal('amount', 10, 2);                     //{0.00 - 99'999'999'.99}
      $table->boolean('cancel')->default(0);
      $table->string('cancel_message')->nullable();         //{0-255}
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
    Schema::dropIfExists('invoice_item');
  }
}
