<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToBestellungTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bestellung', function (Blueprint $table) {
            // 1. Hinzufügen von user_id als Fremdschlüssel zur Tabelle users
            $table->unsignedBigInteger('user_id')->nullable()->after('nr');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // 2. Umbenennung der Felder
            $table->renameColumn('anschriftrechnung', 'rechnungsadresse');
            $table->renameColumn('anschriftlieferschein', 'lieferadresse');

            // 3. Erlauben von NULL-Werten für bestimmte Felder
            $table->string('kundenbestellnr', 100)->nullable()->change();
            $table->string('kommission', 100)->nullable()->change();
            $table->date('lieferdatum')->nullable()->change();
            $table->decimal('gesamtbetrag', 10, 2)->nullable()->change();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bestellung', function (Blueprint $table) {
            // 1. Entfernen des user_id-Felds und des Fremdschlüssels
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // 2. Rückgängig machen der Umbenennung der Felder
            $table->renameColumn('rechnungsadresse', 'anschriftrechnung');
            $table->renameColumn('lieferadresse', 'anschriftlieferschein');

            // 3. Rückgängig machen der NULL-Änderungen
            $table->string('kundenbestellnr', 100)->nullable(false)->change();
            $table->string('kommission', 100)->nullable(false)->change();
            $table->date('lieferdatum')->nullable(false)->change();
            $table->decimal('gesamtbetrag', 10, 2)->nullable(false)->change();
        });
    }
}
