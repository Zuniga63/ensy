<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerInformationTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('customer_information', function (Blueprint $table) {
      $table->id();
      $table->foreignId('customer_id')->constrained('customer')->cascadeOnDelete();
      $table->timestamp('document_expedition_date')->nullable();
      $table->json('document_expedition_place')->nullable();
      $table->string('document_path', 2048)->nullable();
      $table->json('birthplace')->nullable();
      $table->timestamp('birth_date')->nullable();
      $table->string('nacionality', 50)->nullable();
      $table->string('bank_name', 90)->nullable();
      $table->enum('bank_account_type', ['savings', 'current'])->nullable();
      $table->string('bank_account_number')->nullable();
      $table->string('bank_account_holder', 90)->nullable();
      $table->string('bank_account_holder_document', 20)->nullable();
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
    Schema::dropIfExists('customer_information');
  }
}
