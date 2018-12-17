<?php
declare(strict_types=1);

namespace App\Service;

use Evp\Component\Money\Money;

class CurrencyManager
{
    public function convert(Money $money, string $toCurrency): Money
    {
        if ($money->getCurrency() === $toCurrency) {
            return $money;
        }

        $money->setAmount((($money->getAmount()/getenv($money->getCurrency()))*getenv($toCurrency)));

        return $money
            ->ceil()
            ->setCurrency($toCurrency)
        ;
    }
}
