<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerFinancialDataTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('customer_financial_data', function (Blueprint $table) {
      $table->id();
      $table->foreignId('customer_id')->constrained('customer')->cascadeOnDelete();
      $table->string('accupation', 150)->nullable();
      $table->string('position', 150)->nullable();
      $table->string('description')->nullable();
      $table->decimal('salary', 10, 2)->nullable();
      $table->boolean('dependent')->default(false);
      $table->json('company')->nullable();
      $table->timestamp('admission_date')->nullable();
      $table->timestamp('departure_date')->nullable();
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
    Schema::dropIfExists('customer_financial_data');
  }
}
