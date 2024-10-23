<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('artikel_sortimente', function (Blueprint $table) {
            $table->string('artikelnr', 20);
            $table->string('sortiment', 20);

            $table->primary(['artikelnr', 'sortiment']);
            $table->foreign('artikelnr')->references('artikelnr')->on('artikels')->onDelete('cascade');
            $table->foreign('sortiment')->references('bezeichnung')->on('sortimente')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('artikel_sortimente');
    }
};
