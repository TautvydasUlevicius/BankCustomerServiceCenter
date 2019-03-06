<?php
declare(strict_types=1);

namespace App\Service\FileManager;

class CsvParser implements FileParserInterface
{
    public function parseFile(string $pathToFile): array
    {
        return array_map('str_getcsv', file($pathToFile));
    }

    public function getSupportedFileTypes(): array
    {
        return [
            'csv',
        ];
    }
}
