<?php
declare(strict_types=1);

namespace App\Service;

class FileManager
{
    public function getOperationsFromCsv(string $pathToFile): array
    {
        return array_map('str_getcsv', file($pathToFile));
    }

    public function getOperationsFromJson(string $pathToFile): array
    {
        return json_decode(file_get_contents($pathToFile), true);
    }
}
