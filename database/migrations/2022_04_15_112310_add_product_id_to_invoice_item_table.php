<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdToInvoiceItemTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('invoice_item', function (Blueprint $table) {
      $table->foreignId('product_id')->after('invoice_id')->nullable()->constrained('product')->nullOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('invoice_item', function (Blueprint $table) {
      $table->dropForeign(['product_id']);
      $table->dropColumn('product_id');
    });
  }
}
