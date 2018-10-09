<?php
declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class FileValidator
{
    public function checkIfFileExists(string $pathToFile)
    {
        if (!file_exists($pathToFile)) {
            throw new Exception((new FileNotFoundException)->getMessage());
        }
    }

    public function checkIfFileTypeIsValid(string $pathToFile, string $fileType)
    {
        if (pathinfo($pathToFile, PATHINFO_EXTENSION) !== $fileType) {
            throw new Exception($pathToFile . ' is not a valid ' . $fileType . ' file type');
        }
    }
}
