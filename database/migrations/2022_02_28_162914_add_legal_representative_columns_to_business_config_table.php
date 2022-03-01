<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLegalRepresentativeColumnsToBusinessConfigTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('business_config', function (Blueprint $table) {
      $table->string('legal_representative_first_name', 45)->nullable()->after('show_instagram');
      $table->string('legal_representative_last_name', 45)->nullable()->after('legal_representative_first_name');
      $table->string('legal_representative_document', 20)->nullable()->after('legal_representative_last_name');
      /**
       * CC: Cedula de Ciudadanía
       * CE: Cedula de extranjería
       * TI: Tanjeta d Idntidad
       * NIT: Numero de Idntificación Tributario.
       * NIP: Numero de Idntificación Personal
       * PAP: Pasaporte
       */
      $table->enum('legal_representative_document_type', ['CC', 'CE', 'TI', 'NIT', 'NIP', 'PAP'])
        ->default('CC')->after('legal_representative_document');
      $table->enum('legal_representative_sex', ['f', 'm'])->nullable()->after('legal_representative_document_type');
      $table->string('legal_representative_tel', 20)->nullable()->after('legal_representative_document_type');
      $table->string('legal_representative_email')->nullable()->after('legal_representative_tel');
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
        'legal_representative_first_name',
        'legal_representative_last_name',
        'legal_representative_document',
        'legal_representative_document_type',
        'legal_representative_sex',
        'legal_representative_tel',
        'legal_representative_email'
      ]);
    });
  }
}
