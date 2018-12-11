<?php
declare(strict_types=1);

namespace App\Entity;

class Discount
{
    /** @var int */
    private $operationId;

    /** @var float */
    private $discountAmountLeft;

    /** @var int */
    private $operationNumber;

    public function setOperationId(int $operationId): Discount
    {
        $this->operationId = $operationId;

        return $this;
    }

    public function getOperationId(): int
    {
        return $this->operationId;
    }

    public function setDiscountAmountLeft(float $discountAmountLeft): Discount
    {
        $this->discountAmountLeft = $discountAmountLeft;

        return $this;
    }

    public function getDiscountAmountLeft(): float
    {
        return $this->discountAmountLeft;
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
}
