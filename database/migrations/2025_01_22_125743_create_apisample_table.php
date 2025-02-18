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
        Schema::create('apisample', function (Blueprint $table) {
            $table->id();
            $table->string('bezeichnung', 80); // Textfeld mit max. 40 Zeichen
            $table->string('url', 80); // Textfeld mit max. 80 Zeichen
            $table->json('data'); // JSON-Feld
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apisample');
    }
};
