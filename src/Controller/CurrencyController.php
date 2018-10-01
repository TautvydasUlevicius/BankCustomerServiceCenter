<?php
declare(strict_types=1);

namespace App\Controller;

use App\Util\Parameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CurrencyController extends AbstractController
{
    public function convertToEuro(float $amount, string $currency): float
    {
        if ($currency !== Parameter::get('available_currencies')['EURO']) {
            $amount = $amount / Parameter::get('conversion_courses')['EUR' . '_TO_' . $currency];
        }

        return floatval($amount);
    }

    public function convertFromEuro(float $amount, string $currency): float
    {
        if ($currency !== Parameter::get('available_currencies')['EURO']) {
            $amount = $amount * Parameter::get('conversion_courses')['EUR' . '_TO_' . $currency];
        }

        return floatval($amount);
    }
}
