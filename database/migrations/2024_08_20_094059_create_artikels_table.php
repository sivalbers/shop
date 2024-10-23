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
        Schema::create('artikels', function (Blueprint $table) {
            $table->string('artikelnr', 20)->primary();
            $table->string('bezeichnung', 80);
            $table->text('langtext')->nullable();
            $table->enum('status', ['aktiv', 'gesperrt']);
            $table->decimal('verpackungsmenge', 8, 2)->default(1);
            $table->string('einheit', 10);
            $table->decimal('vkpreis', 8, 2);
            $table->string('wgnr', 20);
            $table->foreign('wgnr')->references('wgnr')->on('warengruppen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};
