<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apiaccesslogs', function (Blueprint $table) {

            $table->id(); // Primärschlüssel
            $table->unsignedBigInteger('applicationid'); // Verweis auf applicationauth
            $table->string('endpoint', 255); // Aufgerufener Endpunkt
            $table->enum('httpmethod', ['GET', 'POST', 'PATCH', 'DELETE']); // HTTP-Methode
            $table->dateTime('requesttime')->useCurrent(); // Zeitpunkt des API-Aufrufs
            $table->integer('responsecode'); // HTTP-Statuscode
            $table->string('ipaddress', 45); // IP-Adresse
            $table->foreign('applicationid')->references('id')->on('applicationauth')->onDelete('cascade'); // Fremdschlüssel
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apiaccesslogs');
    }
};
