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
        Schema::create('debitor', function (Blueprint $table) {
            $table->unsignedInteger('nr');
            $table->string('name');
            $table->string('sortiment', 100);
            $table->smallInteger('gesperrt')->unsigned()->default(0);
            $table->timestamps();

            $table->primary(['nr']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debitor');
    }
};
