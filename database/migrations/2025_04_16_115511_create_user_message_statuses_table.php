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
        Schema::create('user_nachrichten_status', function (Blueprint $table) {
            $table->increments('id'); // entspricht int(10) unsigned AUTO_INCREMENT
            $table->unsignedInteger('users_id'); // entspricht int(10) unsigned
            $table->unsignedInteger('nachrichten_id'); // entspricht int(10) unsigned
            $table->boolean('gelesen')->default(false);
            $table->timestamps();

            // Foreign Keys manuell definieren
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('nachrichten_id')->references('id')->on('nachrichten')->onDelete('cascade');

            $table->unique(['users_id', 'nachrichten_id']);

        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_nachrichten_status');
    }
};
