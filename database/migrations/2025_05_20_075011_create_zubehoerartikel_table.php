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
        Schema::create('zubehoerartikel', function (Blueprint $table) {
            $table->string('artikelnr', 20);
            $table->string('zubehoerartikelnr', 20);
            $table->primary(['artikelnr', 'zubehoerartikelnr']); // 👈 Composite Primary Key
            $table->timestamps();
            $table->index('artikelnr');
            $table->index('zubehoerartikelnr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zubehoerartikel');
    }
};
