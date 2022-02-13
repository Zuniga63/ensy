<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerContactTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('customer_contact', function (Blueprint $table) {
      $table->id();
      $table->foreignId('customer_id')->constrained('customer')->cascadeOnDelete();
      $table->string('number', 20);
      $table->string('description', 50)->nullable();
      $table->json('other')->nullable();
      $table->boolean('whatsapp')->default(false);
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
    Schema::dropIfExists('customer_contact');
  }
}
