<?php



namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ImportController extends Controller
{
    private $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/data.csv'); // Korrektes Dateitrennzeichen
    }

    public function importBestellungenInBestellhistorie($csvFilePath = null)
    {
        Log::info('importBestellungenInBestellhistorie');
        try{
            $escapedPath = DB::getPdo()->quote($this->storagePath);
            DB::statement("
                Delete from bestellhistorie;
            ");
/*
            DB::statement("
                LOAD DATA LOCAL INFILE {$escapedPath}
                INTO TABLE bestellhistorie
                FIELDS TERMINATED BY ';'
                ENCLOSED BY '\"'
                LINES TERMINATED BY '\\n'
                IGNORE 1 LINES
                (bestellnr, firma, kundennr, name, rechnungsadresse, lieferadresse, zahlungsart, lieferung, porto, @zeit, bemerkung, benutzername, email, artnr, artikelbezeichnung, eigenschaft, preis_netto, steuersatz, preis_brutto, menge, summe_netto, summe_brutto, kommission, mindermengenzuschlag)
                SET datum = STR_TO_DATE(@zeit, '%Y-%m-%d %H:%i');
            ");
*/
            DB::statement("
            LOAD DATA LOCAL INFILE {$escapedPath}
            INTO TABLE bestellhistorie
            FIELDS TERMINATED BY ';'
            ENCLOSED BY '\"'
            LINES TERMINATED BY '\\n'
            IGNORE 1 LINES
            (bestellnr, firma, kundennr, name, rechnungsadresse, lieferadresse, zahlungsart, lieferung, porto, @zeit, bemerkung, benutzername, email, artnr, artikelbezeichnung, eigenschaft, @preis_netto, steuersatz, @preis_brutto, @menge, @summe_netto, @summe_brutto, kommission, @mindermengenzuschlag)
            SET
                datum = STR_TO_DATE(@zeit, '%Y-%m-%d %H:%i'),
                preis_netto = REPLACE(REPLACE(@preis_netto, '.', ''), ',', '.'),
                preis_brutto = REPLACE(REPLACE(@preis_brutto, '.', ''), ',', '.'),
                menge = REPLACE(REPLACE(@menge, '.', ''), ',', '.'),
                summe_netto = REPLACE(REPLACE(@summe_netto, '.', ''), ',', '.'),
                summe_brutto = REPLACE(REPLACE(@summe_brutto, '.', ''), ',', '.'),
                mindermengenzuschlag = REPLACE(REPLACE(@mindermengenzuschlag, '.', ''), ',', '.');
        ");

            DB::statement("
                INSERT INTO users (email, name, password)
                SELECT DISTINCT trim(email), trim(firma), '\$2y$12\$i1eaxjAgYANjMK0AEDJ/Bere2X/W3nUsPopVbuo0wlRc9fm9K1Xpm' as password FROM bestellhistorie
                ON DUPLICATE KEY UPDATE name = VALUES(name);

                INSERT INTO debitor (nr, name, sortiment)
                SELECT DISTINCT CAST(kundennr AS UNSIGNED), trim(name), 'EWE' as sortiment FROM bestellhistorie
                ON DUPLICATE KEY UPDATE name = VALUES(name);

                INSERT INTO users_debitor (email, debitor_nr)
                SELECT DISTINCT TRIM(bh.email), bh.kundennr
                FROM bestellhistorie bh
                WHERE NOT EXISTS (
                    SELECT 1 FROM users_debitor ud
                    WHERE ud.email = TRIM(bh.email)
                    AND ud.debitor_nr = bh.kundennr
                );


                INSERT IGNORE INTO anschriften (kundennr, firma1, art)
                SELECT
                    bh.kundennr,
                    trim(SUBSTRING_INDEX(bh.lieferadresse, ',', 1)) AS firma1,
                    '' AS art
                FROM bestellhistorie bh;

                INSERT IGNORE INTO anschriften (kundennr, firma1, art)
                SELECT
                    bh.kundennr,
                    trim(SUBSTRING_INDEX(bh.rechnungsadresse, ',', 1)) AS firma1,
                    '' AS art
                FROM bestellhistorie bh;


                insert ignore into bestellungen (nr, datum, kundennr, user_id, rechnungsadresse, lieferadresse, status_id, bemerkung, erpid, kundenbestellnr, kommission)
                SELECT DISTINCT
                    trim(bestellnr),
                    datum,
                    kundennr,
                    (SELECT id FROM users u WHERE u.email = bh.email LIMIT 1) AS user_id,
                    (SELECT id FROM anschriften a WHERE a.kundennr = bh.kundennr LIMIT 1) AS rechnungsadresse,
                    (SELECT id FROM anschriften a WHERE a.kundennr = bh.kundennr LIMIT 1) AS lieferadresse,
                    6 AS Status,
                    trim(bemerkung),

                    'imp' AS erpid,

                    TRIM(
                        SUBSTRING_INDEX(
                            SUBSTRING_INDEX(bh.kommission, 'Kundenbestellnummer:', -1),
                            ',',
                            1
                        )
                    ) AS kundenbestellnr,

                    TRIM(
                        SUBSTRING_INDEX(
                            SUBSTRING_INDEX(bh.kommission, 'Kommission:', -1),
                            ',',
                            1
                        )
                    ) AS kommission_extrahiert
                FROM bestellhistorie bh;

                delete FROM bestellungen_pos
                WHERE bestellnr BETWEEN (SELECT MIN(bestellnr) FROM bestellhistorie)
                    AND (SELECT MAX(bestellnr) FROM bestellhistorie);

                insert into bestellungen_pos (bestellnr, artikelnr, menge, epreis, gpreis, steuer, sort)
                SELECT trim(bestellnr), trim(artnr), menge, preis_netto, summe_netto, 19 as 'steuersatz', 0 as sort FROM `bestellhistorie`;


                UPDATE bestellungen
                SET
                    gesamtbetrag = COALESCE(( SELECT SUM(gpreis) FROM bestellungen_pos WHERE bestellnr = nr ), 0),
                    anzpositionen = COALESCE(( SELECT COUNT(id) FROM bestellungen_pos WHERE bestellnr = nr ), 0)
                ;
            ");

            return "Fertig";
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
