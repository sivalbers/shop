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
        Schema::table('apisample', function (Blueprint $table) {
            // Fügt das Feld 'benutzergruppe' nach dem Feld 'updated_at' hinzu
            $table->string('wgnr', 20)->nullable()->change();
            $table->string('httpmethod')
                  ->nullable()
                  ->comment('POST GET PATCH DELETE')
                  ->after('bezeichnung');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apisample', function (Blueprint $table) {
            // Entfernt das Feld 'benutzergruppe'
            $table->dropColumn('httpmethod');
        });
    }
};
