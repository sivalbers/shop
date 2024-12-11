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
        Schema::create('favoriten_pos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('favoriten_id')->unsigned()->comment('favoriten id'); // Kunden Nummer
            $table->string('artikelnr', 20)->comment('Artikelnummer');
            $table->tinyInteger('sort')->unsigned()->default(0)->comment('Sortierung');
            $table->integer('menge')->unsigned()->default(0)->comment('Menge');

            $table->timestamps();

            $table->foreign('favoriten_id')->references('id')->on('favoriten')->onDelete('cascade');
            $table->index('favoriten_id', 'idx_favoriten_pos_favoriten_id');
            $table->index(['favoriten_id', 'sort'], 'idx_favoriten_pos_favoriten_id_sort');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoriten_pos');
    }
};
