<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bestellungen', function (Blueprint $table) {
            $table->increments('nr'); // Primärschlüssel 'nr'
            $table->timestamp('datum'); // Datum im Timestamp-Format
            $table->integer('kundennr')->unsigned(); // Kunden Nummer
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('rechnungsadresse')->unsigned(); // Rechnungsanschrift
            $table->integer('lieferadresse')->unsigned(); // Lieferanschrift
            $table->tinyInteger('status_id')->unsigned();
            $table->decimal('gesamtbetrag',10,2)->default(0); // Gesamtbetrag
            $table->smallInteger('anzpositionen')->unsigned()->default(0);
            $table->string('kundenbestellnr', 100)->nullable(); // Kundenbestellnummer
            $table->string('kommission', 100)->nullable(); // Kommission
            $table->text('bemerkung')->nullable(); // Bemerkung, optional
            $table->date('lieferdatum')->nullable(); // Gewünschtes Lieferdatum
            $table->timestamps(); // created_at und updated_at

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rechnungsadresse')->references('id')->on('anschriften')->onDelete('cascade');
            $table->foreign('lieferadresse')->references('id')->on('anschriften')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bestellungen');
    }
};
