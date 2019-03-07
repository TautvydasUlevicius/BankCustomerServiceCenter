<?php
declare(strict_types=1);

namespace App\Entity;

use Evp\Component\Money\Money;

class Discount
{
    /**
     * @var int
     */
    private $operationId;

    /**
     * @var int
     */
    private $operationNumber;

    /**
     * @var Money
     */
    private $money;

    public function setOperationId(int $operationId): Discount
    {
        $this->operationId = $operationId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOperationId()
    {
        return $this->operationId;
    }

    public function setOperationNumber(int $operationNumber): Discount
    {
        $this->operationNumber = $operationNumber;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOperationNumber()
    {
        return $this->operationNumber;
    }

    public function setMoney(Money $money): Discount
    {
        $this->money = (new Money())
            ->setAmount($money->getAmount())
            ->setCurrency($money->getCurrency())
        ;

        return $this;
    }

    /**
     * @return Money|null
     */
    public function getMoney()
    {
        return $this->money;
    }
}
