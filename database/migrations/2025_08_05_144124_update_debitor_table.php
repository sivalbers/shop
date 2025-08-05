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
        Schema::table('debitor', function (Blueprint $table) {
            // Fügt das Feld 'erpid' nach dem Feld 'lieferdatum' hinzu
            $table->tinyInteger('abholer')->unsigned()->default(1)->after('sortiment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debitor', function (Blueprint $table) {
            // Entfernt das Feld 'erpid'
            $table->dropColumn('abholer');
        });
    }
};
