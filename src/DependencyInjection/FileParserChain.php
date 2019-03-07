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

    public function addFileParser(FileParserInterface $filerParser, $format)
    {
        $this->fileParsers[$format] = $filerParser;
    }

    public function getFileParser($format)
    {
        if (array_key_exists($format, $this->fileParsers)) {
            return $this->fileParsers[$format];
        }
    }

    public function getSupportedDataFileTypes(): array
    {
        $supportedDataFileTypes = [];

        foreach ($this->fileParsers as $key => $value) {
            $supportedDataFileTypes[$key] = $this->fileParsers[$key]->getSupportedFileTypes();
        }

        return $supportedDataFileTypes;
    }
}
