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
        Schema::table('bestellungen', function (Blueprint $table) {
            // Fügt das Feld 'erpid' nach dem Feld 'lieferdatum' hinzu
            $table->string('kopieempfaenger')
                  ->nullable()
                  ->comment('Email Empfaenger für Bestellbestätigung')
                  ->after('lieferdatum');
            $table->tinyInteger('abholer')->unsigned()->default(0)->after('kopieempfaenger');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bestellungen', function (Blueprint $table) {
            // Entfernt das Feld 'erpid'
            $table->dropColumn('kopieempfaenger');
            $table->dropColumn('abholer');
        });
    }
};
