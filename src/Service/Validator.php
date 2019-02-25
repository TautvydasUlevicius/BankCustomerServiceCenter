<?php
declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class Validator
{
    const SUPPORTED_DATA_FILE_TYPES = [
        'csv_csv',
        'json_json',
        'json_txt'
    ];

    public function validateOperationsFile(string $fileLocation, string $dataType)
    {
        $this->checkIfFileExists($fileLocation);
        $this->validateTypes($fileLocation, $dataType);
    }

    public function checkIfFileExists(string $pathToFile)
    {
        if (!file_exists($pathToFile)) {
            throw new Exception((new FileNotFoundException)->getMessage());
        }
    }

    public function validateTypes(string $pathToFile, string $dataType)
    {
        $data = strtolower($dataType);
        $file = pathinfo($pathToFile, PATHINFO_EXTENSION);

        $isSupported = 0;
        foreach (self::SUPPORTED_DATA_FILE_TYPES as $types) {
            if ($types === $data . '_' . $file) {
                $isSupported = 1;
            }
        }

        if ($isSupported === 0) {
            throw new Exception(
                'File type and data type do not match or one of the types provided is not supported'
            );
        }
    }
}
