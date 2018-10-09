<?php
declare(strict_types=1);

namespace App\Controller;

use App\Validator\FileValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CsvFileController extends AbstractController
{
    const FILE_CSV = 'csv';

    private $fileValidator;

    public function __construct(FileValidator $fileValidator)
    {
        $this->fileValidator = $fileValidator;
    }

    public function getOperationsFromFile(string $pathToFile): array
    {
        $this->fileValidator->checkIfFileExists($pathToFile);
        $this->fileValidator->checkIfFileTypeIsValid($pathToFile, self::FILE_CSV);

        return array_map('str_getcsv', file($pathToFile));
    }
}
