<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoryTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('product_category', function (Blueprint $table) {
      $table->id();
      $table->foreignId('main_category_id')->nullable()->constrained('product_category')->nullOnDelete();
      $table->string('name', 45);
      $table->string('slug', 45);
      $table->string('icon', 2048)->nullable();
      $table->unsignedTinyInteger('level')->default(0);
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
    Schema::dropIfExists('product_category');
  }
}
