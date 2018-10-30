<?php
declare(strict_types=1);

namespace App\Service;

class CsvFileManager
{
    public function getOperationsFromFile(string $pathToFile): array
    {
        return array_map('str_getcsv', file($pathToFile));
    }
}
