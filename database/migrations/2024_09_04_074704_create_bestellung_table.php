<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBestellungTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bestellung', function (Blueprint $table) {
            $table->id('nr'); // Primärschlüssel 'nr'
            $table->timestamp('datum'); // Datum im Timestamp-Format
            $table->unsignedBigInteger('kundennr'); // Kunden Nummer
            $table->unsignedBigInteger('anschriftrechnung'); // Rechnungsanschrift
            $table->unsignedBigInteger('anschriftlieferschein'); // Lieferanschrift
            $table->unsignedBigInteger('status'); // Status
            $table->string('kundenbestellnr', 100); // Kundenbestellnummer
            $table->string('kommission', 100); // Kommission
            $table->text('bemerkung')->nullable(); // Bemerkung, optional
            $table->decimal('gesamtbetrag', 10, 2); // Gesamtbetrag
            $table->date('lieferdatum'); // Gewünschtes Lieferdatum
            $table->timestamps(); // created_at und updated_at

            // Fremdschlüssel-Beziehungen
            $table->foreign('anschriftrechnung')->references('id')->on('anschriften')->onDelete('cascade');
            $table->foreign('anschriftlieferschein')->references('id')->on('anschriften')->onDelete('cascade');
            $table->foreign('status')->references('id')->on('status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bestellung');
    }
}
