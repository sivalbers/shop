<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apilogs', function (Blueprint $table) {
            $table->id(); // Primärschlüssel
            $table->string('httpmethod')->nullable()->comment('POST GET PATCH DELETE');
            $table->string('version')->nullable(); // Version der API
            $table->string('pfad')->nullable(); // Pfad der Anfrage
            $table->string('key')->nullable(); // Schlüssel
            $table->string('session')->nullable(); // Session-ID
            $table->string('token')->nullable(); // API-Token
            $table->text('data')->nullable(); // Eingehende Daten (JSON oder anderer Typ)
            $table->text('response')->nullable(); // Antwortdaten (JSON oder anderer Typ)
            $table->timestamps(); // Erstellt `created_at` und `updated_at`



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apilogs');
    }
}
