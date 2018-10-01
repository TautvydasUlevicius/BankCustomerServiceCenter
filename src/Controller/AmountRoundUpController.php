<?php
declare(strict_types=1);

namespace App\Controller;

use App\Util\Parameter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AmountRoundUpController extends AbstractController
{
    public function roundUpAmount(float $amount, string $currency): float
    {
        if ($currency === Parameter::get('available_currencies')['JAPANESE_YEN']) {
            $amount = ceil($amount);
        } else {
            $amount = ceil($amount * pow(10, 2)) / pow(10, 2);
        }

        return $amount;
    }
}
