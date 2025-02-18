<?php

namespace App\Helpers;

class SortimentHelper
{
    // Definiere die Farbzuteilung
    protected static $farben = [
        'ewe' => 'text-ewe-gruen',
        'oowv' => 'text-blue-500',
        'tk' => 'text-pink-500',
        'be' => 'text-orange-500',
        'wn' => 'text-green-500',
    ];

    protected static $bgFarben = [
        'ewe' => 'bg-ewe-gruen',
        'oowv' => 'bg-blue-500',
        'tk' => 'bg-pink-500',
        'be' => 'bg-orange-500',
        'wn' => 'bg-green-500',
    ];


    /**
     * Gibt die passenden Farbkategorien für ein Sortiment zurück
     * @param string $sortiment
     * @return string
     */
    public static function getColorClass(string $sortiment): string
    {
        // Zerlege mögliche zusammengesetzte Werte ("EWE BE") in Einzelteile
        $teile = explode(' ', strtolower($sortiment));

        // Ordne die Farben zu und verbinde sie mit Leerzeichen
        $farben = array_map(fn($teil) => self::$farben[$teil] ?? 'text-gray-500', $teile);

        return implode(' ', $farben);
    }

    public static function getBGColorClass(string $sortiment): string
    {
        // Zerlege mögliche zusammengesetzte Werte ("EWE BE") in Einzelteile
        $teile = explode(' ', strtolower($sortiment));

        // Ordne die Farben zu und verbinde sie mit Leerzeichen
        $farben = array_map(fn($teil) => self::$bgFarben[$teil] ?? 'bg-gray-500', $teile);

        return implode(' ', $farben);
    }
}
