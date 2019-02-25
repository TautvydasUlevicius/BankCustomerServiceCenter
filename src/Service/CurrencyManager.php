<?php
declare(strict_types=1);

namespace App\Service;

use Evp\Component\Money\Money;

class CurrencyManager
{
    public function convert(Money $money, string $toCurrency): Money
    {
        if ($money->getCurrency() === $toCurrency) {
            return $money->ceil(Money::getFraction($toCurrency));
        }

        return $money
            ->div($_ENV[$money->getCurrency()])
            ->mul($_ENV[$toCurrency])
            ->ceil(Money::getFraction($toCurrency))
            ->setCurrency($toCurrency)
        ;
    }
}
