<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('sortimente');

        Schema::create('sortimente', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('bezeichnung', 20);
            $table->string('anzeigename', 20);
            $table->tinyInteger('abholung')->unsigned()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sortimente');
    }
};
