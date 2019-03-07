<?php
declare(strict_types=1);

namespace App\Service\Validation;

class CurrencyValidator
{
    private $supportedCurrencies;

    public function __construct(array $supportedCurrencies)
    {
        $this->supportedCurrencies = $supportedCurrencies;
    }

    public function checkIfCurrencyIsSupported(string $currency): bool
    {
        foreach (array_keys($this->supportedCurrencies) as $supportedCurrency) {
            if ($currency === $supportedCurrency) {
                return true;
            }
        }

        throw new \Exception('Unsupported Currency Provided');
    }
}
