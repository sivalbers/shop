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

    protected static $textBgFarben = [
        'ewe' => 'text-sky-600',
        'oowv' => 'text-white',
        'tk' => 'text-white',
        'be' => 'text-white',
        'wn' => 'text-white',
    ];

    protected static $gradientFarben = [
        'ewe' => 'from-ewe-gruen/100 to-ewe-gruen/0',
        'oowv' => 'from-blue-500/100 to-blue-500/0',
        'tk' => 'from-pink-500/70 to-pink-500/0',
        'be' => 'from-orange-500/100 to-orange-500/0',
        'wn' => 'from-green-500/100 to-green-500/0',
    ];

    protected static $gradientFarbenAsc = [
        'ewe' => 'from-ewe-gruen/0 to-ewe-gruen/70',
        'oowv' => 'from-blue-500/0 to-blue-500/70',
        'tk' => 'from-pink-500/0 to-pink-500/70',
        'be' => 'from-orange-500/0 to-orange-500/70',
        'wn' => 'from-green-500/0 to-green-500/70',
    ];


    /**
     * Gibt die passenden Farbkategorien für ein Sortiment zurück
     * @param string $sortiment
     * @return string
     */
    public static function getColorClass(string $sortiment): string
    {
        $teile = explode(' ', strtolower($sortiment));

        foreach ($teile as $teil) {
            if (isset(self::$farben[$teil])) {
                return self::$farben[$teil];
            }
        }

        return 'text-gray-500'; // Fallback
    }

    public static function getBGColorClass(string $sortiment): string
    {
        $teile = explode(' ', strtolower($sortiment));

        foreach ($teile as $teil) {
            if (isset(self::$bgFarben[$teil])) {
                return self::$bgFarben[$teil];
            }
        }

        return 'bg-gray-500'; // Fallback
    }

    public static function getTextZuBGColorClass(string $sortiment): string
    {
        // Zerlege mögliche zusammengesetzte Werte ("EWE BE") in Einzelteile
        $teile = explode(' ', strtolower($sortiment));

        foreach ($teile as $teil) {
            if (isset(self::$textBgFarben[$teil])) {
                return self::$textBgFarben[$teil];
            }
        }

        return 'bg-gray-500'; // Fallback
    }

    public static function getGradientBG(string $sortiment): string
    {
        $teile = explode(' ', strtolower($sortiment));

        foreach ($teile as $teil) {
            if (isset(self::$gradientFarben[$teil])) {
                return self::$gradientFarben[$teil];
            }
        }

        return 'from-gray-500/100 to-gray-500/0'; // Fallback

    }

    public static function getGradientBGAsc(string $sortiment): string
    {
        $teile = explode(' ', strtolower($sortiment));

        foreach ($teile as $teil) {
            if (isset(self::$gradientFarbenAsc[$teil])) {
                return self::$gradientFarbenAsc[$teil];
            }
        }

        return 'from-gray-500/100 to-gray-500/0'; // Fallback

    }
}
