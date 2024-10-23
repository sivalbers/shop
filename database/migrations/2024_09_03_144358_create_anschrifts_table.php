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
        Schema::create('anschriften', function (Blueprint $table) {
            $table->id();
            $table->integer('kundennr');
            $table->unsignedInteger('usersid');
            $table->string('kurzbeschreibung', 100);
            $table->string('firma1', 80);
            $table->string('firma2', 80)->nullable();
            $table->string('firma3', 80)->nullable();
            $table->string('strasse', 80);
            $table->string('plz', 8);
            $table->string('stadt', 80);
            $table->string('land', 80);
            $table->boolean('standard')->default(false);
            $table->enum('art', ['Lieferadresse', 'Rechnungsadresse']);
            $table->timestamps();

            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anschrifts');
    }
};
