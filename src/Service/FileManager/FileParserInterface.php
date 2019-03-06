<?php
declare(strict_types=1);

namespace App\Service\FileManager;

interface FileParserInterface
{
    public function parseFile(string $pathToFile): array;

    public function getSupportedFileTypes(): array;
}
