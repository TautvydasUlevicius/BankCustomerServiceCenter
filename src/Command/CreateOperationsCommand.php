<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Operation;
use App\Service\CommissionManager;
use App\Service\CurrencyManager;
use App\Service\DateChecker;
use App\Service\OperationManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateOperationsCommand extends ContainerAwareCommand
{
    private $dateChecker;
    private $mainCurrency;
    private $currencyManager;
    private $operationManager;
    private $commissionManager;

    public function __construct(
        string $mainCurrency,
        DateChecker $dateChecker,
        CurrencyManager $currencyManager,
        OperationManager $operationManager,
        CommissionManager $commissionManager
    ) {
        parent::__construct();

        $this->dateChecker = $dateChecker;
        $this->mainCurrency = $mainCurrency;
        $this->currencyManager = $currencyManager;
        $this->operationManager = $operationManager;
        $this->commissionManager = $commissionManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:calculate-commissions')
            ->setHelp('This command gets operations from a csv file and calculates the commission for the operations')
            ->setDescription('Gets operations from a CSV file and calculates the commission')
            ->setDefinition(
                new InputDefinition([
                    new InputOption(
                        'location',
                        'l',
                        InputOption::VALUE_REQUIRED,
                        'File location path'
                    ),
                ])
            )
            ->addArgument(
                'format',
                InputArgument::REQUIRED,
                'Input data format'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileLocation = $input->getOptions();
        $dataType = $input->getArguments();
        $operationObjects = $this->operationManager->createOperationsFromFile(
            $fileLocation['location'],
            $dataType['format']
        );

        /** @var Operation $operationObject */
        foreach ($operationObjects as $operationObject) {
            $operationObject->getMoney()->setAmount(
                $this->currencyManager->convert(
                    $operationObject->getMoney(),
                    $this->mainCurrency
                )->getAmount()
            );
        }

        $calculatedCommissions = $this->commissionManager->calculateCommission($operationObjects);

        $counter = 0;

        foreach ($calculatedCommissions as $commission) {
                $output->writeln($this->currencyManager->convert(
                    $commission,
                    $operationObjects[$counter]->getMoney()->getCurrency()
                ));
                $counter++;
        }
    }
}
