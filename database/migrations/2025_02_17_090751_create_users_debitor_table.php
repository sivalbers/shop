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
        Schema::create('users_debitor', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->require();
            $table->unsignedInteger('debitor_nr');
            $table->smallInteger('rolle')->unsigned()->default(1);
            $table->smallInteger('standard')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_debitor');
    }
};
