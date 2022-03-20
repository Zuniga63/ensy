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
      $table->float('quantity', 8, 4);
      $table->string('description');
      $table->decimal('unit_value', 10, 2);
      $table->decimal('amount', 10, 2);
      $table->boolean('cancel')->default(0);
      $table->string('cancel_message')->nullable();
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
