<?php

use Illuminate\Support\Facades\Storage;


if (!function_exists('imageExists')) {
    function imageExists($artikelnr)
    {
        if (Storage::exists("public/products/$artikelnr.jpg")) {
            return "$artikelnr.jpg";
        } elseif (Storage::exists("public/products/$artikelnr.png")) {
            return "$artikelnr.png";
        }

        return '';
    }

}

