<?php
declare(strict_types=1);

namespace App\Command;

use App\Controller\IndexController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CsvImportCommand extends Command
{
    private $indexController;

    public function __construct(
        ?string $name = null,
        IndexController $indexController
    ) {
        parent::__construct($name);
        $this->indexController = $indexController;
    }

    protected function configure()
    {
        $this
            ->setName('app:import-csv-file')
            ->setHelp('This command gets location of the file and passes it onto the index controller')
            ->setDescription('Gets information from a CSV file')
            ->setDefinition(
                new InputDefinition([
                    new InputOption(
                        'location',
                        'l',
                        InputOption::VALUE_REQUIRED,
                        'File location path'
                    )
                ])
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileLocation = $input->getOptions('location');
        $this->indexController->index($fileLocation['location']);
    }
}
