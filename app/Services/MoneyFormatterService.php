<?php

namespace App\Services;

class MoneyFormatterService
{
    /**
     * Create a new class instance.
     */

    public static function format($amount, $sign = true)
    {
        $value = $amount / 100; // convertir de centavos a pesos
        if (!$sign) {
            return number_format($value, 2, ",", ".");
        } else {
            return '$' . number_format($value, 2, ",", ".");
        }
    }
}
