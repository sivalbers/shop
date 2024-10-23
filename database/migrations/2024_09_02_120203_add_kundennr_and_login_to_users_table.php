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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('kundennr')->comment('Kundennummer');
            $table->string('login', 100)->unique()->comment('Login');
            $table->integer('role')->default(0)->comment('Benutzergruppe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kundennr');
            $table->dropColumn('login');
            $table->dropColumn('role');
        });
    }
};
