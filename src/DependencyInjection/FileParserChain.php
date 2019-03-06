<?php
declare(strict_types=1);

namespace App\DependencyInjection;

use App\Service\FileManager\FileParserInterface;

class FileParserChain
{
    private $fileParsers;
    private $supportedDataFileTypes;

    public function __construct()
    {
        $this->fileParsers = [];
        $this->supportedDataFileTypes = [];
    }

    public function addFileParser(FileParserInterface $filerParser, $format)
    {
        $this->fileParsers[$format] = $filerParser;
        $this->supportedDataFileTypes[$format] = $filerParser->getSupportedFileTypes();
    }

    public function getFileParser($format)
    {
        if (array_key_exists($format, $this->fileParsers)) {
            return $this->fileParsers[$format];
        }
    }

    public function getSupportedDataFileTypes(): array
    {
        return $this->supportedDataFileTypes;
    }
}
