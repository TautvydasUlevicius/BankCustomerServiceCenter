<?php
declare(strict_types=1);

namespace App\Util;

class AmountRoundUp
{
    const JAPANESE_YEN = 'JPY';

    public function roundUpAmount(float $amount, string $currency): float
    {
        if ($currency === self::JAPANESE_YEN) {
            return ceil($amount);
        }

        return ceil($amount * pow(10, 2)) / pow(10, 2);
    }
}
