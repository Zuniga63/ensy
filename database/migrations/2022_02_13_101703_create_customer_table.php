<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('customer', function (Blueprint $table) {
      $table->id();
      $table->string('first_name', 50);
      $table->string('last_name', 50)->nullable();
      $table->string('document_number', 20)->nullable()->unique();
      $table->string('email')->nullable()->unique();
      $table->string('image_path', 2048)->nullable();
      $table->enum('sex', ['f', 'm'])->nullable();
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
    Schema::dropIfExists('customer');
  }
}
