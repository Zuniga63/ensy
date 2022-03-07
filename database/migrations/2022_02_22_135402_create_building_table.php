<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('building', function (Blueprint $table) {
      $table->id();
      $table->foreignId('owner_id')->nullable()->constrained('customer')->nullOnDelete();
      $table->foreignId('building_admin_id')->nullable()->constrained('building_admin')->nullOnDelete();
      $table->foreignId('country_department_id')->nullable()->constrained('country_department')->nullOnDelete();
      $table->foreignId('town_id')->nullable()->constrained('town')->nullOnDelete();
      $table->foreignId('town_district_id')->nullable()->constrained('town_district')->nullOnDelete();
      $table->string('image_path', 2048)->nullable();
      $table->string('description')->nullable();
      $table->json('features')->nullable();
      $table->json('address')->nullable();
      $table->enum('building_type', ['house', 'apartment', 'business'])->nullable();
      $table->unsignedTinyInteger('socioeconomic')->nullable();
      $table->unsignedTinyInteger('rooms')->nullable();
      $table->unsignedTinyInteger('bathrooms')->nullable();
      $table->unsignedTinyInteger('parking_lots')->nullable();
      $table->unsignedFloat('area')->nullable();
      $table->unsignedFloat('private_area')->nullable();
      $table->unsignedTinyInteger('floor')->nullable();
      $table->date('antiquity')->nullable();
      $table->decimal('lease_fee', 10, 2)->default(0.00);
      $table->decimal('admin_fee', 10, 2)->default(0.00);
      $table->float('commission', 3, 2)->default(0.00);
      $table->string('insurance_carrier', 45)->nullable();
      $table->string('insurance_type', 45)->nullable();
      $table->float('insurance_commission', 3, 2)->default(0.00);
      $table->boolean('available')->default(false);
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
    Schema::dropIfExists('building');
  }
}
