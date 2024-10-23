<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUstToArtikelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('artikels', function (Blueprint $table) {
            // Füge das Feld 'USt' als Dezimalfeld hinzu mit 19,00 als Standardwert
            $table->decimal('steuer', 5, 2)->default(19.00)->after('vkpreis');
            $table->decimal('bestand', 5, 2)->default(0.00)->after('steuer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('artikels', function (Blueprint $table) {
            // Entferne das Feld 'USt' bei einem Rollback der Migration
            $table->dropColumn('steuer');
            $table->dropColumn('bestand');
        });
    }
}
