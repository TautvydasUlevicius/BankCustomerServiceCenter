<?php
declare(strict_types=1);

namespace App\Service;

class CurrencyManager
{
    public function convert(float $amount, string $fromCurrency, string $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        return ($amount/getenv($fromCurrency))*getenv($toCurrency);
    }
}
