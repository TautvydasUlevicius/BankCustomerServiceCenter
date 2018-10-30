<?php
declare(strict_types=1);

namespace App\Util;

use Symfony\Component\Dotenv\Dotenv;

class AmountRoundUp
{
    const JAPANESE_YEN = 'JPY';

    private $dotenv;

    public function __construct()
    {
        $this->dotenv = new Dotenv();
        $this->dotenv->load('../BankCustomerServiceCenter/config/parameters.env');
    }

    public function roundUpAmount(float $amount, string $currency): float
    {
        if ($currency === self::JAPANESE_YEN) {
            return ceil($amount);
        }

        return ceil($amount * pow(10, 2)) / pow(10, 2);
    }
}
