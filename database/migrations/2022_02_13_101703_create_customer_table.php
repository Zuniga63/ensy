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
      /**
       * CC: Cedula de Ciudadanía
       * CE: Cedula de extranjería
       * TI: Tanjeta d Idntidad
       * NIT: Numero de Idntificación Tributario.
       * NIP: Numero de Idntificación Personal
       * PAP: Pasaporte
       */
      $table->enum('document_type', ['CC', 'CE', 'TI', 'NIT', 'NIP', 'PAP'])->default('CC');
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
