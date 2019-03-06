<?php
declare(strict_types=1);

namespace App\Entity;

use Evp\Component\Money\Money;

class Operation
{
    /**
     * @var int
     */
    private $operationId;

    /**
     * @var string
     */
    private $operationDate;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var string
     */
    private $operationType;

    /**
     * @var Money
     */
    private $money;

    public function setOperationId(int $operationId): Operation
    {
        $this->operationId = $operationId;

        return $this;
    }

    /**
     * @return int/null
     */
    public function getOperationId()
    {
        return $this->operationId;
    }

    public function setDate(string $operationDate): Operation
    {
        $this->operationDate = $operationDate;

        return $this;
    }

    /**
     * @return string/null
     */
    public function getDate()
    {
        return $this->operationDate;
    }

    public function setUserId(string $userId): Operation
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return string/null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserType(string $userType): Operation
    {
        $this->userType = $userType;

        return $this;
    }

    /**
     * @return string/null
     */
    public function getUserType()
    {
        return $this->userType;
    }

    public function setOperationType(string $operationType): Operation
    {
        $this->operationType = $operationType;

        return $this;
    }

    /**
     * @return string/null
     */
    public function getOperationType()
    {
        return $this->operationType;
    }

    public function setMoney(Money $money): Operation
    {
        $this->money = (new Money())
            ->setAmount($money->getAmount())
            ->setCurrency($money->getCurrency())
        ;

        return $this;
    }

    /**
     * @return Money/null
     */
    public function getMoney()
    {
        return $this->money;
    }
}
