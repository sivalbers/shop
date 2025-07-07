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
        Schema::table('wghelper', function (Blueprint $table) {
            $table->string('wgnr', 20)->nullable()->change();
            if (!Schema::hasColumn('wghelper', 'name')) {
                $table->string('name', 80)
                    ->nullable()
                    ->comment('')
                    ->after('wgnr');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wghelper', function (Blueprint $table) {
            if (Schema::hasColumn('wghelper', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
