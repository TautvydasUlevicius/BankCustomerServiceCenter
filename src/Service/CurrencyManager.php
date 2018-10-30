<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Dotenv\Dotenv;

class CurrencyManager
{
    private $dotenv;

    public function __construct()
    {
        $this->dotenv = new Dotenv();
        $this->dotenv->load('../BankCustomerServiceCenter/config/parameters.env');
    }

    public function convert(float $amount, string $fromCurrency, string $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        return ($amount/getenv($fromCurrency))*getenv($toCurrency);
    }
}
