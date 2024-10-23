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
        Schema::table('anschriften', function (Blueprint $table) {
            // 1. Entferne das Feld 'usersid'
            $table->dropColumn('usersid');

            // 2. Passe die Länge des Feldes 'land' an (auf 5 Zeichen begrenzen)
            $table->string('land', 5)->change();

            // 3. Erlaube, dass das Feld 'art' auch leer sein kann
            $table->enum('art', ['Lieferadresse', 'Rechnungsadresse', ''])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anschriften', function (Blueprint $table) {
            // 1. Füge das Feld 'usersid' wieder hinzu
            $table->unsignedInteger('usersid');

            // 2. Setze die Länge des Feldes 'land' wieder auf 80 Zeichen
            $table->string('land', 80)->change();

            // 3. Setze das Feld 'art' zurück auf die ursprüngliche Definition
            $table->enum('art', ['Lieferadresse', 'Rechnungsadresse'])->change();
        });
    }
};
