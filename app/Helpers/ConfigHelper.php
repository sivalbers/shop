<?php

use App\Models\Config;

if (!function_exists('configGet')) {
    function configGet($option): string
        {
                return Config::userString($option);
        }
    }

if (!function_exists('configSet')) {
    function configSet($option, $value)
        {
                return Config::setUserString($option, $value);
        }
    }
