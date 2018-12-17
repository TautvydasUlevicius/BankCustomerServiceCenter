<?php
declare(strict_types=1);

namespace App\Entity;

use Evp\Component\Money\Money;

class Discount
{
    /** @var int */
    private $operationId;

    /** @var int */
    private $operationNumber;

    /** @var Money */
    private $money;

    public function setOperationId(int $operationId): Discount
    {
        $this->operationId = $operationId;

        return $this;
    }

    public function getOperationId(): int
    {
        return $this->operationId;
    }

    public function setOperationNumber(int $operationNumber): Discount
    {
        $this->operationNumber = $operationNumber;

        return $this;
    }

    public function getOperationNumber(): int
    {
        return $this->operationNumber;
    }

    public function setDiscount(float $amount, string $currency): Discount
    {
        $this->money = (new Money())
            ->setAmount($amount)
            ->setCurrency($currency)
        ;

        return $this;
    }

    public function getDiscount(): Money
    {
        return $this->money;
    }
}
