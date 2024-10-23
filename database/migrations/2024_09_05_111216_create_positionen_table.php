<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePositionenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positionen', function (Blueprint $table) {
            // Primärschlüssel
            $table->id(); // autoincrement

            // Felder der Tabelle
            $table->unsignedBigInteger('bestellnr')->comment('Bestellnr.'); // Referenziert die Bestellung
            $table->string('artikelnr', 20)->comment('Artikel-Nr.');
            $table->decimal('menge', 10, 2)->comment('Menge');
            $table->decimal('epreis', 10, 2)->comment('E-Preis'); // Einzelpreis
            $table->decimal('gpreis', 10, 2)->comment('G-Preis'); // Gesamtpreis
            $table->decimal('steuer', 5, 2)->comment('MwSt'); // Mehrwertsteuer
            $table->unsignedSmallInteger('sort')->comment('Sortierung');

            // Timestamps (falls gewünscht)
            $table->timestamps();

            // Sekundärindex für bestellnr und sort
            $table->index(['bestellnr', 'sort']);

            // Fremdschlüssel: bestellnr referenziert bestellung(nr)
            $table->foreign('bestellnr')->references('nr')->on('bestellung')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('positionen');
    }
}
