<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerReferenceTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('customer_reference', function (Blueprint $table) {
      $table->id();
      $table->foreignId('customer_id')->constrained('customer')->cascadeOnDelete();
      $table->string('first_name', 50);
      $table->string('last_name', 50)->nullable();
      $table->string('document_number', 20)->nullable();
      $table->enum('document_type', ['CC', 'CE', 'TI', 'NIT', 'NIP', 'PAP'])->default('CC');
      $table->string('email')->nullable();
      $table->string('phone', 20)->nullable();
      $table->json('address')->nullable();
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
    Schema::dropIfExists('customer_reference');
  }
}
