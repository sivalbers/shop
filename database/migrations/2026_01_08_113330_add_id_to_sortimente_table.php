<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('sortimente', 'bezeichnung')) {
            Schema::table('sortimente', function (Blueprint $table) {
                $table->dropPrimary(['bezeichnung']);
            });
        }

        if (! Schema::hasColumn('sortimente', 'id')) {
            Schema::table('sortimente', function (Blueprint $table) {
                $table
                    ->tinyInteger('id')
                    ->unsigned()
                    //->primary()
                    ->first();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sortimente', 'id')) {
            Schema::table('sortimente', function (Blueprint $table) {
                $table->dropPrimary(['id']);
                $table->dropColumn('id');
            });
        }
    }
};
