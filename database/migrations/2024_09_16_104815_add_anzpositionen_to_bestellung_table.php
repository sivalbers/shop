<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnzpositionenToBestellungTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bestellung', function (Blueprint $table) {
            $table->integer('anzpositionen')->default(0)->after('gesamtbetrag');
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
            $table->dropColumn('anzpositionen');
        });
    }
}
