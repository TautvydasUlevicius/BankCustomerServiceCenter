<?php
declare(strict_types=1);

namespace App\DependencyInjection;

use App\Service\FileManager\FileParserInterface;

class FileParserChain
{
    private $fileParsers;

    public function __construct()
    {
        $this->fileParsers = [];
    }

    public function addFileParser(FileParserInterface $filerParser, $alias)
    {
        $this->fileParsers[$alias] = $filerParser;
    }

    public function getFileParser($alias)
    {
        if (array_key_exists($alias, $this->fileParsers)) {
            return $this->fileParsers[$alias];
        }
    }
}
