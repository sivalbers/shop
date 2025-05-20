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
        Schema::create('ersatzartikel', function (Blueprint $table) {
            $table->string('artikelnr', 20);
            $table->string('ersatzartikelnr', 20);
            $table->primary(['artikelnr', 'ersatzartikelnr']); // 👈 Composite Primary Key
            $table->timestamps();
            $table->index('artikelnr');
            $table->index('ersatzartikelnr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ersatzartikel');
    }
};
