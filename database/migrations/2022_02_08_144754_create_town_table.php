<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTownTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('town', function (Blueprint $table) {
      $table->id();
      $table->foreignId('country_department_id')->constrained('country_department');
      $table->unsignedSmallInteger('code');
      $table->string('name');
      $table->enum('type', ['capital', 'municipality', 'sidewalk', 'corregimiento'])->default('municipality');
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
    Schema::dropIfExists('town');
  }
}
