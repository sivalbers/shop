<?php


if (!function_exists('formatPreis')) {

    function formatPreis($value, $decimal = 2)
    {
        return number_format($value, $decimal, ',', '.'); // Beispiel: 12345 wird 123,45
    }
}

if (!function_exists('formatMenge')) {
    function formatMenge($value)
    {
        return number_format($value, 0, ',', '.'); // Ohne Dezimalstellen
    }
}


if (!function_exists('formatGPreis')) {
    function formatGPreis($value)
    {
        return number_format($value, 2, ',', '.'); // Ohne Dezimalstellen
    }
}

