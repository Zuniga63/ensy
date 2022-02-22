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
      $table->foreignId('building_id')->constrained('building')->cascadeOnDelete();
      $table->string('admin_first_name', 45)->nullable();
      $table->string('admin_last_name', 45)->nullable();
      $table->string('admin_document_number')->nullable();
      $table->json('phones')->nullable();
      $table->string('email')->nullable();
      $table->decimal('admin_fee', 10, 2)->default(0.00);
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
