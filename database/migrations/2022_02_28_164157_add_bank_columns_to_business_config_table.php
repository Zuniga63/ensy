<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankColumnsToBusinessConfigTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('business_config', function (Blueprint $table) {
      $table->string('bank_name', 45)->nullable()->after('nit_date_of_renovation');
      $table->string('bank_account_number')->nullable()->after('bank_name');
      $table->enum('bank_account_type', ['savings', 'current'])->default('savings')->after('bank_account_number');
      $table->string('bank_account_holder', 90)->nullable()->after('bank_account_type');
      $table->string('bank_account_holder_document', 20)->nullable()->after('bank_account_holder');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('business_config', function (Blueprint $table) {
      $table->dropColumn([
        'bank_name',
        'bank_account_number',
        'bank_account_type',
        'bank_account_holder',
        'bank_account_holder_document'
      ]);
    });
  }
}
