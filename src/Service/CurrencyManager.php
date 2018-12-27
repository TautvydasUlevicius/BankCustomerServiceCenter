<?php
declare(strict_types=1);

namespace App\Service;

use Evp\Component\Money\Money;

class CurrencyManager
{
    public function convert(Money $money, string $toCurrency)
    {
        if ($money->getCurrency() === $toCurrency) {
            return $money;
        }

        return $money
            ->div($_ENV[$money->getCurrency()])
            ->mul($_ENV[$toCurrency])
            ->setCurrency($toCurrency)
            ->ceil(Money::getFraction($toCurrency))
        ;
    }
}
