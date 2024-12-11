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
        Schema::create('favoriten', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('kundennr')->unsigned()->comment('Kundennummer'); // Kunden Nummer
            $table->integer('user_id')->unsigned()->nullable()->comment('User-ID wenn für User'); // Benutzernummer
            $table->string('name', 80);

            $table->timestamps();

            $table->index('kundennr', 'idx_favoriten_kundennr');
            $table->index(['kundennr', 'user_id'], 'idx_favoriten_kundennr_user_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favoriten');
    }
};
