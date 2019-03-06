<?php
declare(strict_types=1);

namespace App\Service;

use Evp\Component\Money\Money;

class CurrencyManager
{
    private $configuration;

    public function __construct(
        array $configuration
    ) {
        $this->configuration = $configuration;
    }

    public function convert(Money $money, string $toCurrency): Money
    {
        if ($money->getCurrency() === $toCurrency) {
            return $money;
        }

        return $money
            ->div($this->configuration[$money->getCurrency()])
            ->mul($this->configuration[$toCurrency])
            ->ceil(Money::getFraction($toCurrency))
            ->setCurrency($toCurrency)
        ;
    }
}
