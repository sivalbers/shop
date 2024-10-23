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
        Schema::create('status', function (Blueprint $table) {
            $table->id(); // Primary Key 'id'
            $table->string('bezeichnung', 30)->default('Bezeichnung'); // Text[30] mit Standardwert 'Bezeichnung'
            $table->timestamps(); // Fügt created_at und updated_at Spalten hinzu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status');
    }
};
