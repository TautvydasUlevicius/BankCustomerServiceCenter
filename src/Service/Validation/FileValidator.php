<?php
declare(strict_types=1);

namespace App\Service\Validation;

use App\DependencyInjection\FileParserChain;

class FileValidator
{
    private $fileParserChain;

    public function __construct(FileParserChain $fileParserChain)
    {
        $this->fileParserChain = $fileParserChain;
    }

    public function validateOperationsFile(string $fileLocation, string $dataType)
    {
        if ($this->checkIfFileExists($fileLocation) === false ||
        $this->validateTypes($fileLocation, $dataType) === false) {
            print_r('Either the file was not found, or the file/data type is not supported');
            die();
        }
    }

    public function checkIfFileExists(string $pathToFile): bool
    {
        if (!file_exists($pathToFile)) {
            return false;
        }

        return true;
    }

    public function validateTypes(string $pathToFile, string $dataType): bool
    {
        $supportedDataFileTypes = $this->fileParserChain->getSupportedDataFileTypes();
        $data = strtolower($dataType);
        $file = pathinfo($pathToFile, PATHINFO_EXTENSION);

        if (!isset($supportedDataFileTypes[$data])) {
            return false;
        }

        foreach ($supportedDataFileTypes[$data] as $fileTypes) {
            if ($file === $fileTypes) {
                return true;
            }
        }

        return false;
    }
}
