<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Operation;
use App\Util\FileValidator;
use Evp\Component\Money\Money;

class OperationManager
{
    private $fileValidator;
    private $csvFileManager;

    public function __construct(
        FileValidator $fileValidator,
        CsvFileManager $csvFileManager
    ) {
        $this->fileValidator = $fileValidator;
        $this->csvFileManager = $csvFileManager;
    }

    public function createOperationsFromFile(string $fileLocation): array
    {
        $this->fileValidator->checkIfFileExists($fileLocation);
        $this->fileValidator->checkIfFileTypeIsValid($fileLocation, $_ENV['SUPPORTED_FILE_TYPE']);
        $operations = $this->csvFileManager->getOperationsFromFile($fileLocation);

        return $this->createArrayOfOperationObjects($operations);
    }

    protected function createArrayOfOperationObjects(array $operations): array
    {
        $counter = 0;

        $arrayOfOperationObjects = [];

        foreach ($operations as $operation) {
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
