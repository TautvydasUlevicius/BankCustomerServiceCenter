<?php
declare(strict_types=1);

namespace App\Service;

use App\DependencyInjection\FileParserChain;
use App\Entity\Operation;
use App\Service\Validation\CurrencyValidator;
use App\Service\Validation\FileValidator;
use Evp\Component\Money\Money;

class OperationManager
{
    private $validator;
    private $fileManager;
    private $currencyValidator;

    public function __construct(
        FileValidator $validator,
        FileParserChain $fileParserChain,
        CurrencyValidator $currencyValidator
    ) {
        $this->validator = $validator;
        $this->fileManager = $fileParserChain;
        $this->currencyValidator = $currencyValidator;
    }

    public function createOperationsFromFile(string $fileLocation, string $dataType): array
    {
        $this->validator->validateOperationsFile($fileLocation, $dataType);
        $operations = $this->fileManager->getFileParser($dataType)->parseFile($fileLocation);

        $counter = 0;

        $arrayOfOperationObjects = [];

        foreach ($operations as $operation) {
            $this->currencyValidator->checkIfCurrencyIsSupported($operation[5]);
            $arrayOfOperationObjects[$counter] = (new Operation())
                ->setOperationId($counter)
                ->setDate($operation[0])
                ->setUserId($operation[1])
                ->setUserType($operation[2])
                ->setOperationType($operation[3])
                ->setMoney(new Money($operation[4], $operation[5]))
            ;
            $counter++;
        }

        return $arrayOfOperationObjects;
    }
}
