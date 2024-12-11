<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warengruppen', function (Blueprint $table) {
            $table->string('wgnr', 20)->primary();
            $table->string('bezeichnung', 80);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warengruppen');
    }
};
