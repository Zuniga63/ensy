<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('bank_account', function (Blueprint $table) {
      $table->id();
      $table->foreignId('bank_id')->constrained('bank')->cascadeOnDelete();
      $table->string('number', 50)->unique();
      $table->enum('type', ['saving', 'current']);
      $table->string('holder_first_name', 50);
      $table->string('holder_last_name', 50)->nullable();
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
    Schema::dropIfExists('bank_account');
  }
}
