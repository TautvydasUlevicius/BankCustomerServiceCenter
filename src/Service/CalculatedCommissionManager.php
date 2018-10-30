<?php
declare(strict_types=1);

namespace App\Service;

class CalculatedCommissionManager
{
    public function printOutCalculatedCommission(float $calculateCommission)
    {
        print_r($calculateCommission . PHP_EOL);
    }
}
