<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingAdminTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('building_admin', function (Blueprint $table) {
      $table->id();
      $table->string('name', 45);
      $table->string('address')->nullable();
      $table->string('admin_first_name', 45)->nullable();
      $table->string('admin_last_name', 45)->nullable();
      $table->string('admin_document_number', 20)->nullable();
      $table->json('phones')->nullable();
      $table->string('email')->nullable();
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
    Schema::dropIfExists('building_admin');
  }
}
