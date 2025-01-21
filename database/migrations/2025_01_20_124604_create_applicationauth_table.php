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
        Schema::create('applicationauth', function (Blueprint $table) {

            $table->id('id');
            $table->string('applicationname', 255)->nullable(); // Name der Anwendung
            $table->string('apikey', 255); // API-Key
            $table->string('sessionid', 255)->nullable(); // Session-ID
            $table->dateTime('sessionexpiry')->nullable(); // Ablauf der Session
            $table->dateTime('lastlogin')->nullable(); // Letzter erfolgreicher Login
            $table->enum('status', ['active', 'inactive', 'revoked'])->default('active'); // Status
            $table->text('allowedendpoints')->nullable(); // Erlaubte Endpunkte
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicationauth');
    }
};
