<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
            CREATE PROCEDURE GetBestellungSummary(IN bestellnr INT, OUT anzpositionen INT, OUT gesamtbetrag DECIMAL(10,2))
            BEGIN
                DECLARE gpreis_sum DECIMAL(10,2);
                DECLARE position_count INT;

                -- Berechnung der Summe der gpreis-Felder und der Anzahl der Positionen in einer Abfrage
                SELECT SUM(gpreis), COUNT(*)
                INTO gpreis_sum, position_count
                FROM bestellung_pos
                WHERE bestellnr = bestellnummer;

                -- Update der Tabelle Bestellungen
                UPDATE bestellungen
                SET gesamtbetrag = gpreis_sum, anzpositionen = position_count
                WHERE nr = bestellnummer;

                -- Rückgabe der berechneten Werte
                SELECT position_count AS anzpositionen, gpreis_sum AS gesamtbetrag;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetBestellungSummary');
    }
};
