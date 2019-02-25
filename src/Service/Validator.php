<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class Validator
{
    const SUPPORTED_FILE_TYPES = [
        'csv',
        'json',
        'txt',
    ];

    const SUPPORTED_DATA_TYPES = [
        'csv',
        'json',
    ];

    public function validateOperationsFile(string $fileLocation, string $dataType)
    {
        $this->checkIfFileExists($fileLocation);
        $this->checkIfFileTypeIsSupported($fileLocation);
        $this->checkIfDataTypeIsSupported($dataType);
        $this->compareFileTypeAndDataType($fileLocation, $dataType);
    }

    public function checkIfFileExists(string $pathToFile)
    {
        if (!file_exists($pathToFile)) {
            throw new Exception((new FileNotFoundException)->getMessage());
        }
    }

    public function checkIfFileTypeIsSupported(string $pathToFile)
    {
        $isSupported = 0;
        foreach (self::SUPPORTED_FILE_TYPES as $type) {
            if (pathinfo($pathToFile, PATHINFO_EXTENSION) === $type) {
                $isSupported = 1;
            }
        }

        if ($isSupported === 0) {
            throw new Exception($pathToFile . ' is not a supported file type');
        }
    }

    public function checkIfDataTypeIsSupported(string $dataType)
    {
        $isSupported = 0;
        foreach (self::SUPPORTED_DATA_TYPES as $type) {
            if (strtolower($dataType) === $type) {
                $isSupported = 1;
            }
        }

        if ($isSupported === 0) {
            throw new Exception($dataType . ' is not a supported data type');
        }
    }

    public function compareFileTypeAndDataType(string $pathToFile, string $dataType)
    {
        $data = strtolower($dataType);
        $file = pathinfo($pathToFile, PATHINFO_EXTENSION);
        if ($data !== $file) {
            if ($data === 'json' && $file !== 'txt') {
                throw new Exception('File type and data type do not match');
            }
        }
    }
}
