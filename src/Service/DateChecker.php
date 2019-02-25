<?php
declare(strict_types=1);

namespace App\Service;

use DateTime;

class DateChecker
{
    public function checkIfTwoDatesOnSameWeek(string $firstDate, string $secondDate): bool
    {
        if (((new DateTime($firstDate))->format('oW')) === ((new DateTime($secondDate))->format('oW'))) {
            return true;
        }

        return false;
    }
}
