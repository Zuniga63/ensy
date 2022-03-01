<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTaxColumnsToBusinessConfigTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('business_config', function (Blueprint $table) {
      $table->string('nit', 45)->nullable()->after('legal_representative_email');
      $table->string('nit_name', 150)->nullable()->after('nit');
      $table->date('nit_date_of_renovation')->nullable()->after('nit_name');
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
        'nit',
        'nit_name',
        'nit_date_of_renovation',
      ]);
    });
  }
}
