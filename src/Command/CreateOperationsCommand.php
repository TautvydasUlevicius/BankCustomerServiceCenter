<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\CommissionManager;
use App\Service\CsvFileManager;
use App\Service\CurrencyManager;
use App\Service\DiscountManager;
use App\Service\OperationManager;
use App\Util\AmountRoundUp;
use App\Util\DateChecker;
use App\Util\FileValidator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Dotenv\Dotenv;

class CreateOperationsCommand extends ContainerAwareCommand
{
    private $dotenv;
    private $dateChecker;
    private $fileValidator;
    private $amountRoundUp;
    private $csvFileManager;
    private $currencyManager;
    private $discountManager;
    private $operationManager;
    private $commissionManager;

    public function __construct(
        DateChecker $dateChecker,
        FileValidator $fileValidator,
        AmountRoundUp $amountRoundUp,
        CsvFileManager $csvFileManager,
        CurrencyManager $currencyManager,
        DiscountManager $discountManager,
        OperationManager $operationManager,
        CommissionManager $commissionManager
    ) {
        parent::__construct();

        $this->dotenv = new Dotenv();
        $this->dateChecker = $dateChecker;
        $this->fileValidator = $fileValidator;
        $this->amountRoundUp = $amountRoundUp;
        $this->csvFileManager = $csvFileManager;
        $this->currencyManager = $currencyManager;
        $this->discountManager = $discountManager;
        $this->operationManager = $operationManager;
        $this->commissionManager = $commissionManager;

        $this->dotenv->load('../BankCustomerServiceCenter/config/parameters.env');
    }

    protected function configure()
    {
        $this
            ->setName('app:calculate-commissions')
            ->setHelp('This command gets operations from a csv file and creates an entity')
            ->setDescription('Gets operations from a CSV file and creates an entity')
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
        $fileLocation = $input->getOptions();
        $this->fileValidator->checkIfFileExists($fileLocation['location']);
        $this->fileValidator->checkIfFileTypeIsValid($fileLocation['location'], getenv('SUPPORTED_FILE_TYPE'));

        $operationsArray = $this->csvFileManager->getOperationsFromFile($fileLocation['location']);
        $operationObjects = $this->operationManager->createArrayOfOperationObjects($operationsArray);

        foreach ($operationObjects as $operationObject) {
            $operationObject->setAmount(
                $this->currencyManager->convert(
                    $operationObject->getAmount(),
                    $operationObject->getCurrency(),
                    getenv('MAIN_CURRENCY')
                )
            );
        }

        $discountInformation = $this->discountManager->calculateDiscountForOperations($operationObjects);
        $calculatedCommissions = $this->commissionManager->calculateCommission($operationObjects, $discountInformation);

        $counter = 0;
        foreach ($calculatedCommissions as $commission) {
            $output->writeln(
                $this->amountRoundUp->roundUpAmount(
                    $this->currencyManager->convert(
                        $commission,
                        getenv('MAIN_CURRENCY'),
                        $operationObjects[$counter]->getCurrency()
                    ),
                    $operationObjects[$counter]->getCurrency()
                )
            );

            $counter++;
        }
    }
}
