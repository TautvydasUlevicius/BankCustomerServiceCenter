<?php
declare(strict_types=1);

namespace App\Service\FileManager;

class JsonParser implements FileParserInterface
{
    public function parseFile(string $pathToFile): array
    {
        return json_decode(file_get_contents($pathToFile), true);
    }
}
