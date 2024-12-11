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
        Schema::create('nachrichten', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kurztext', 100); // Text mit maximal 100 Zeichen
            $table->text('langtext')->nullable(); // Langtext, kann null sein
            $table->date('von')->nullable(); // Startdatum, kann null sein
            $table->date('bis')->nullable(); // Enddatum, kann null sein
            $table->text('links')->nullable(); // Langtext für Links, kann null sein
            $table->enum('prioritaet', ['normal', 'mittel', 'hoch'])->default('normal'); // Prioritaet mit Standardwert 'normal'
            $table->boolean('startseite')->default(true); // Boolean für ja/nein
            $table->integer('kundennr')->unsigned()->nullable(); // Kundennummer, kann null sein
            $table->smallInteger('lagerort')->unsigned()->nullable();
            $table->boolean('mitlogin')->default(false); // Kundennummer, kann null sein
            $table->timestamps(); // Erstellt created_at und updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nachrichten');
    }
};
