<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessConfigTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('business_config', function (Blueprint $table) {
      $table->id();
      $table->string('name', 45)->nullable();
      $table->string('logo', 2048)->nullable();
      $table->string('favicon', 2048)->nullable();
      $table->string('phone', 20)->nullable();
      $table->boolean('show_phone')->default(false);
      $table->string('address')->nullable();
      $table->boolean('show_address')->default(false);
      $table->string('email')->nullable();
      $table->string('show_email')->default(false);
      $table->string('whatsapp', 20)->nullable();
      $table->boolean('show_wathsapp')->default(false);
      $table->string('facebook_nick')->nullable();
      $table->string('facebook_link', 2048)->nullable();
      $table->boolean('show_facebook')->default(false);
      $table->string('instagram_nick')->nullable();
      $table->string('instagram_link', 2048)->nullable();
      $table->boolean('show_instagram')->default(false);
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
    Schema::dropIfExists('business_config');
  }
}
