<?php
declare(strict_types=1);

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private $csvFileController;
    private $currencyController;
    private $amountRoundUpController;
    private $commissionController;

    public function __construct(
        CsvFileController $csvFileController,
        CurrencyController $currencyController,
        AmountRoundUpController $amountRoundUpController,
        CommissionController $commissionController
    ) {
        $this->csvFileController = $csvFileController;
        $this->currencyController = $currencyController;
        $this->amountRoundUpController = $amountRoundUpController;
        $this->commissionController = $commissionController;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(string $pathToFile)
    {
        $operations = $this->csvFileController->getOperationsFromFile($pathToFile);

        for ($i = 0; $i < count($operations); $i++) {
            $operationWeekNumber = (new DateTime($operations[$i][0]))->format('oW');

            $counter = 0;
            $operationSumInTotal = 0;
            $times = 1;
            while ($counter < $i) {
                $weekNumber = (new DateTime($operations[$counter][0]))->format('oW');
                if ($operations[$i][1] === $operations[$counter][1] &&
                    $operations[$i][3] === $operations[$counter][3] &&
                    $operationWeekNumber === $weekNumber) {
                    $times++;
                    $operationAmount = $this->currencyController->convertToEuro(
                        floatval($operations[$counter][4]),
                        $operations[$counter][5]
                    );
                    $operationSumInTotal = $operationSumInTotal + $operationAmount;
                }
                $counter++;
            }
            array_push($operations[$i], $times, $operationSumInTotal);
        }

        foreach ($operations as $operation) {
            $operationMoney = $this->currencyController->convertToEuro(
                floatval($operation[4]),
                $operation[5]
            );
            if ($operation[3] === 'cash_in') {
                $commissionFee = $this->commissionController->moneyDeposit($operationMoney);
            } elseif ($operation[3] === 'cash_out' && $operation[2] === 'legal') {
                $commissionFee = $this->commissionController->cashClearingForLegalPeople($operationMoney);
            } else {
                $commissionFee = $this->commissionController->cashClearingForNaturalPeople(
                    $operationMoney,
                    $operation[6],
                    $operation[7]
                );
            }
            $commissionFee = $this->currencyController->convertFromEuro($commissionFee, $operation[5]);
            $commissionFee = $this->amountRoundUpController->roundUpAmount($commissionFee, $operation[5]);
            print_r($commissionFee);
            print_r(PHP_EOL);
        }
    }
}
