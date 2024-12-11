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
        Schema::create('artikel', function (Blueprint $table) {
            $table->string('artikelnr', 20)->primary();
            $table->string('bezeichnung', 80);
            $table->text('langtext')->nullable();
            $table->integer('verpackungsmenge')->unsigned()->default(100);
            $table->string('einheit', 10);
            $table->decimal('vkpreis',10,2)->default(0);
            $table->decimal('steuer',4,2)->default(19);
            $table->integer('bestand')->unsigned()->default(0);
            $table->string('wgnr', 20);
            $table->boolean('gesperrt')->default(false);

            $table->foreign('wgnr')->references('wgnr')->on('warengruppen');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikel');
    }
};
