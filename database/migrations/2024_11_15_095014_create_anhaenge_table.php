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
        Schema::create('anhaenge', function (Blueprint $table) {
            $table->id();
            $table->string('artikelnr', 20);
            $table->string('dateiname', 100);
            $table->text('beschreibung')->nullable();
            $table->tinyInteger('art')->unsigned()->default(0);
            $table->tinyInteger('sort')->unsigned()->default(0);
            $table->boolean('gesperrt')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anhaenge');
    }
};
