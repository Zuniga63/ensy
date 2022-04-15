<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('product', function (Blueprint $table) {
      $table->id();
      $table->foreignId('brand_id')->nullable()->constrained('brand')->nullOnDelete();
      $table->string('name', 90);
      $table->string('slug', 90);
      $table->string('image', 2048)->nullable();
      $table->string('ref', 100)->nullable();
      $table->string('barcode', 100)->nullable();
      $table->string('description')->nullable();
      $table->unsignedDecimal('price', 10, 2)->default(0);                //{0.00 - 99 999 999.99}
      $table->unsignedDecimal('price_with_discount', 10, 2)->nullable();
      $table->boolean('inventoriable')->default(0);
      $table->mediumInteger('stock')->default(0);               //{-8.388.608 - 8.388.607}
      $table->unsignedSmallInteger('min_stock')->default(0);    //{0 - 65.535}
      $table->boolean('highlight')->default(0);
      $table->boolean('is_new')->default(0);
      $table->boolean('publish')->default(0);
      $table->unsignedSmallInteger('order')->default(0);
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
    Schema::dropIfExists('product');
  }
}
