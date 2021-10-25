<?php
declare(strict_types = 1);

namespace App\Broker\ExportConverter;

use App\Broker\BrokerExport;
use OfxParser\Entities\AbstractEntity;
use OfxParser\Entities\Investment\Transaction\Banking;
use OfxParser\Entities\Investment\Transaction\BuyStock;
use OfxParser\Entities\Investment\Transaction\Income;
use OfxParser\Entities\Investment\Transaction\SellStock;
use OfxParser\Parsers\Investment;
use OfxParser\Utils;
use SplFileInfo;

class InteractiveBrokersConverter implements ExportConverterInterface
{
    public const NAME = 'Interactive Brokers';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertExport(SplFileInfo $brokerFile): BrokerExport
    {
        $export = new BrokerExport();
        $ofx = (new Investment)->loadFromFile($brokerFile->getPathname());
        foreach ($ofx->bankAccounts as $bankAccount) {
            foreach ($bankAccount->statement->transactions as $transaction) {
                $this->addTransaction($export, $transaction);
            }
        }

        return $export;
    }

    private function addTransaction(BrokerExport $export, AbstractEntity $transaction): void
    {
        if ($transaction instanceof BuyStock) {
            $this->addBuyAction($export, $transaction);
        } elseif ($transaction instanceof SellStock) {
            $this->addSellAction($export, $transaction);
        } elseif ($transaction instanceof Banking) {
            $this->addBankAction($export, $transaction);
        } elseif ($transaction instanceof Income) {
            $this->addIncomeAction($export, $transaction);
        }
    }

    private function addBuyAction(BrokerExport $export, BuyStock $transaction): void
    {
        $export->addBuyAction(
            $transaction->uniqueId,
            $transaction->settlementDate ?? $transaction->tradeDate,
            $transaction->securityId,
            $transaction->currency,
            (float) $transaction->units,
            (float) $transaction->unitPrice,
            (float) (string) $transaction->xmlNode->INVBUY->COMMISSION,
            (float) $transaction->currencyRate,
            (float) (string) $transaction->xmlNode->INVBUY->TAXES,
        );
    }

    private function addSellAction(BrokerExport $export, SellStock $transaction): void
    {
        $export->addSellAction(
            $transaction->uniqueId,
            $transaction->settlementDate ?? $transaction->tradeDate,
            $transaction->securityId,
            $transaction->currency,
            (float) $transaction->units,
            (float) $transaction->unitPrice,
            (float) (string) $transaction->xmlNode->INVBUY->COMMISSION,
            (float) $transaction->currencyRate,
            (float) (string) $transaction->xmlNode->INVBUY->TAXES,
        );
    }

    private function addBankAction(BrokerExport $export, Banking $transaction): void
    {
        if ($transaction->type === 'DEP') {
            $export->addDeposit(
                $transaction->date,
                $transaction->currency,
                (float) $transaction->amount,
                (float) $transaction->currencyRate,
            );
        } elseif ($transaction->type === 'WIT') { // TODO: The transaction type for withdraw is guessed
            $export->addWithdrawal(
                $transaction->date,
                $transaction->currency,
                (float) $transaction->amount,
                (float) $transaction->currencyRate,
            );
        } elseif ($transaction->type === 'INT') {
            $export->addBrokerCost(
                $transaction->date,
                $transaction->currency,
                (float) $transaction->amount,
                // For now must be 'Flatex Interest', since PDT matches based on description
                'Flatex Interest',
                (float) $transaction->currencyRate,
            );
        }
    }

    private function addIncomeAction(BrokerExport $export, Income $transaction): void
    {
        if ($transaction->incomeType !== 'DIV') {
            return;
        }

        $export->addDividendAction(
            $transaction->settlementDate ?? $transaction->tradeDate,
            $transaction->securityId,
            $transaction->currency,
            (float) $transaction->total,
            (float) $transaction->currencyRate,
        );
    }
}
