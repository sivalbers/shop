<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('bestellungen_pos', function (Blueprint $table) {
            // Primärschlüssel
            $table->id(); // autoincrement

            // Felder der Tabelle
            $table->integer('bestellnr')->unsigned()->comment('Bestellnr.'); // Referenziert die Bestellung
            $table->string('artikelnr', 20)->comment('Artikel-Nr.');
            $table->integer('menge')->unsigned()->comment('Menge');
            $table->decimal('epreis',10,2)->comment('E-Preis'); // Einzelpreis
            $table->decimal('gpreis',10,2)->comment('G-Preis'); // Gesamtpreis
            $table->decimal('steuer',4,2)->comment('MwSt'); // Mehrwertsteuer
            $table->smallInteger('sort')->unsigned()->comment('Sortierung');

            // Timestamps (falls gewünscht)
            $table->timestamps();

            // Sekundärindex für bestellnr und sort
            $table->index(['bestellnr', 'sort']);

            // Fremdschlüssel: bestellnr referenziert bestellung(nr)
            $table->foreign('bestellnr')->references('nr')->on('bestellungen')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bestellungen_pos');
    }
};
