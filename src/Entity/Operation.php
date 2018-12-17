<?php
declare(strict_types=1);

namespace App\Entity;

use Evp\Component\Money\Money;

class Operation
{
    /** @var int */
    private $operationId;

    /** @var string */
    private $operationDate;

    /** @var string */
    private $userId;

    /** @var string */
    private $userType;

    /** @var string */
    private $operationType;

    /** @var Money */
    private $money;

    public function setOperationId(int $operationId): Operation
    {
        $this->operationId = $operationId;

        return $this;
    }

    public function getOperationId():int
    {
        return $this->operationId;
    }

    public function setDate(string $operationDate): Operation
    {
        $this->operationDate = $operationDate;

        return $this;
    }

    public function getDate(): string
    {
        return $this->operationDate;
    }

    public function setUserId(string $userId): Operation
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserType(string $userType): Operation
    {
        $this->userType = $userType;

        return $this;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function setOperationType(string $operationType): Operation
    {
        $this->operationType = $operationType;

        return $this;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function setMoney(string $amount, string $currency): Operation
    {
        $this->money = (new Money())
            ->setAmount($amount)
            ->setCurrency($currency)
        ;

        return $this;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }
}
