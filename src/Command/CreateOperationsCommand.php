<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Operation;
use App\Service\CalculatedCommissionManager;
use App\Service\CommissionManager;
use App\Service\CsvFileManager;
use App\Service\CurrencyManager;
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
    private $operationManager;
    private $commissionManager;
    private $calculatedCommissionManager;

    public function __construct(
        DateChecker $dateChecker,
        FileValidator $fileValidator,
        AmountRoundUp $amountRoundUp,
        CsvFileManager $csvFileManager,
        CurrencyManager $currencyManager,
        OperationManager $operationManager,
        CommissionManager $commissionManager,
        CalculatedCommissionManager $calculatedCommissionManager
    ) {
        parent::__construct();

        $this->dotenv = new Dotenv();
        $this->dateChecker = $dateChecker;
        $this->fileValidator = $fileValidator;
        $this->amountRoundUp = $amountRoundUp;
        $this->csvFileManager = $csvFileManager;
        $this->currencyManager = $currencyManager;
        $this->operationManager = $operationManager;
        $this->commissionManager = $commissionManager;
        $this->calculatedCommissionManager = $calculatedCommissionManager;

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
        $this->fileValidator->checkIfFileTypeIsValid(
            $fileLocation['location'],
            getenv('SUPPORTED_OPERATIONS_FILE_TYPE')
        );

        $arrayOfOperations = $this->csvFileManager->getOperationsFromFile($fileLocation['location']);

        $previousOperations = [];
        $operationCounter = 0;
        foreach ($arrayOfOperations as $operation) {
            $operation = $this->operationManager->createOperationObject($operation);

            $operationAmountInEur = $this->currencyManager->convert(
                $operation->getAmount(),
                $operation->getCurrency(),
                getenv('MAIN_CURRENCY')
            );

            $previousOperations[$operationCounter]['UserId'] = $operation->getUserId();
            $previousOperations[$operationCounter]['UserType'] = $operation->getUserType();
            $previousOperations[$operationCounter]['Date'] = $operation->getDate();
            $previousOperations[$operationCounter]['Amount'] = $operationAmountInEur;
            $previousOperations[$operationCounter]['OperationType'] = $operation->getOperationType();
            $previousOperations[$operationCounter]['OperationPerWeek'] = 1;
            $previousOperations[$operationCounter]['TotalOperationSum'] = 0;

            $counter = 0;

            while ($counter < $operationCounter) {
                if ($this->checkIfOperationsSameGroup($previousOperations[$counter], $operation) === true) {
                    $previousOperations[$operationCounter]['OperationPerWeek']++;
                    $previousOperations[$operationCounter]['TotalOperationSum'] +=
                        $previousOperations[$counter]['Amount'];
                }
                $counter++;
            }
            $operationCounter++;
            $commission = $this->calculateOperationCommission($previousOperations);
            $commission = $this->currencyManager->convert(
                $commission,
                getenv('MAIN_CURRENCY'),
                $operation->getCurrency()
            );
            $commission = $this->amountRoundUp->roundUpAmount($commission, $operation->getCurrency());
            $this->calculatedCommissionManager->printOutCalculatedCommission($commission);
        }
    }

    private function checkIfOperationsSameGroup(array $previousOperation, Operation $operation): bool
    {
        if ($previousOperation['UserId'] === $operation->getUserId() &&
            $previousOperation['UserType'] === $operation->getUserType() &&
            $previousOperation['OperationType'] == $operation->getOperationType() &&
            $this->dateChecker->checkIfTwoDatesOnSameWeek($previousOperation['Date'], $operation->getDate()) === true
        ) {
            return true;
        }

        return false;
    }

    private function calculateOperationCommission(array $operations)
    {
        foreach ($operations as $operation) {
            if ($operation['OperationType'] === 'cash_in') {
                $commission = $this->commissionManager->moneyDeposit($operation['Amount']);
            } elseif ($operation['OperationType'] === 'cash_out' && $operation['UserType'] === 'legal') {
                $commission = $this->commissionManager->cashClearingForLegalPeople($operation['Amount']);
            } else {
                $commission = $this->commissionManager->cashClearingForNaturalPeople(
                    $operation['Amount'],
                    $operation['OperationPerWeek'],
                    $operation['TotalOperationSum']
                );
            }
        }
        return $commission;
    }
}
