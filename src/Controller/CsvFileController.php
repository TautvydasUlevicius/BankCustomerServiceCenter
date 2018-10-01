<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CsvFileController extends AbstractController
{
    public function getOperationsFromFile(string $fileLocation): array
    {
        return (array_map('str_getcsv', file($fileLocation)));
    }
}
