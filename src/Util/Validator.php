<?php
declare(strict_types=1);

namespace App\Util;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class Validator
{
    public function checkIfFileExists(string $pathToFile)
    {
        if (!file_exists($pathToFile)) {
            throw new Exception((new FileNotFoundException)->getMessage());
        }
    }

    public function checkIfFileTypeIsSupported(string $pathToFile)
    {
        $supportedFileTypes = explode(',', $_ENV['SUPPORTED_FILE_TYPES']);

        $isSupported = 0;
        foreach ($supportedFileTypes as $type) {
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
        $supportedDataTypes = explode(',', $_ENV['SUPPORTED_DATA_TYPES']);

        $isSupported = 0;
        foreach ($supportedDataTypes as $type) {
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
                throw new \Exception('File type and data type do not match');
            }
        }
    }
}
