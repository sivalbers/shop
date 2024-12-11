<?php

use Illuminate\Support\Facades\Log;


if (!function_exists('debugLog')) {
    function debugLog($level, $message, $context = [])
    {
        if ((int) env('DEBUG_LEVEL_EBENE', 0) >= $level) {
            Log::info("{$level}: {$message}", $context);
        }
    }
}


