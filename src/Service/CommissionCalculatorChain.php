<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Operation;
use App\Util\DateChecker;
use App\Util\Validator;

class CommissionCalculatorChain
{
    private $services;

    public function __construct($services)
    {
        $this->services = $services;
    }

    public function getCommissionManager(): CommissionManager
    {
        return $this->services[0];
    }

    public function getCurrencyManager(): CurrencyManager
    {
        return $this->services[1];
    }

    public function getDiscountManager(): DiscountManager
    {
        return $this->services[2];
    }

    public function getFileManager(): FileManager
    {
        return $this->services[3];
    }

    public function getOperationManager(): OperationManager
    {
        return $this->services[4];
    }

    public function getDateChecker(): DateChecker
    {
        return $this->services[5];
    }

    public function getValidator(): Validator
    {
        return $this->services[6];
    }

    public function getOperation(): Operation
    {
        return $this->services[7];
    }
}
