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
        Schema::create('config', function (Blueprint $table) {
            $table->id(); // Primärschlüssel 'id', autoincrement
            $table->string('option', 80)->comment('Option'); // Maximal 20 Zeichen für das Feld 'option'
            $table->integer('kundennr')->unsigned()->nullable()->comment('Kundennummer');
            $table->integer('userid')->unsigned()->nullable()->comment('User-ID');
            $table->string('value', 100)->nullable()->comment('Value'); // Maximal 100 Zeichen, kann null sein
            $table->json('json_data')->nullable();

            // Timestamps
            $table->timestamps();

            // Sekundärschlüssel: Kombination aus id, kundennr und userid
            $table->index(['option', 'kundennr', 'userid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config');
    }
};
