<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingImageTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('building_image', function (Blueprint $table) {
      $table->id();
      $table->foreignId('building_id')->constrained('building')->cascadeOnDelete();
      $table->string('image_path', 2048);
      $table->string('description')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('building_image');
  }
}
