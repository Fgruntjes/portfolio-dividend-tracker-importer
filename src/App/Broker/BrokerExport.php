<?php
declare(strict_types = 1);

namespace App\Broker;

use DateTimeInterface;
use Ramsey\Uuid\Uuid;
use SplFileObject;
use SplTempFileObject;

/**
 * This class represents the manual input actions in PDT
 */
class BrokerExport
{
    private SplFileObject $file;

    public function __construct()
    {
        $this->file = new SplTempFileObject();
        $this->file->fputcsv([
            'Datum',
            'Tijd',
            'Valutadatum',
            'Product',
            'ISIN',
            'Omschrijving',
            'FX',
            'Mutatie',
            '',
            'Saldo',
            '',
            'Order ID',
        ]);
    }

    public function getStream(bool $readFromStart = true): SplFileObject
    {
        if ($readFromStart) {
            $this->file->rewind();
        }
        return $this->file;
    }

    public function addBuyAction(
        string $transactionId,
        DateTimeInterface $date,
        string $isn,
        string $currency,
        float $numberOfShares,
        float $pricePerShare,
        float $transactionCost,
        ?float $exchangeRate = null,
        ?float $transactionTax = null,
    ): void {
        // Datum,Tijd,Valutadatum,Product,ISIN,Omschrijving,FX,Mutatie,,Saldo,,Order Id
        // 13-04-2021,14:48,13-04-2021,VANGUARD S&P500,IE00B3XXRP09,DEGIRO transactiekosten,,EUR,"-0,02",EUR,"31,52",6ba241f2-f066-46e1-97ae-a5b6f9bb0d48
        // 13-04-2021,14:48,13-04-2021,VANGUARD S&P500,IE00B3XXRP09,DEGIRO transactiekosten,,EUR,"-2,00",EUR,"31,54",6ba241f2-f066-46e1-97ae-a5b6f9bb0d48
        // 13-04-2021,14:48,13-04-2021,VANGUARD S&P500,IE00B3XXRP09,"Koop 1 @ 65,661 EUR",,EUR,"-65,66",EUR,"33,54",6ba241f2-f066-46e1-97ae-a5b6f9bb0d48
        $this->appendCsv(
            $date,
            $isn,
            $isn,
            'DEGIRO transactiekosten',
            null,
            $currency,
            abs($transactionCost) * -1,
            $currency,
            null,
            $transactionId,
        );
        $this->appendCsv(
            $date,
            $isn,
            $isn,
            "Koop {$numberOfShares} @ {$this->priceToString($pricePerShare)} {$currency}",
            null,
            $currency,
            $numberOfShares * $pricePerShare * -1,
            $currency,
            null,
            $transactionId,
        );
    }

    public function addSellAction(
        string $transactionId,
        DateTimeInterface $date,
        string $isn,
        string $currency,
        float $numberOfShares,
        float $pricePerShare,
        float $transactionCost,
        ?float $exchangeRate = null,
        ?float $transactionTax = null,
    ): void {
        // TODO: I need an export of DEGIRO and IB that contains at least one sell
    }

    public function addDividendAction(
        DateTimeInterface $date,
        string $isn,
        string $currency,
        float $dividendReceived,
        ?float $exchangeRate = null,
        ?float $dividendTax = null,
    ): void {
        // Datum,Tijd,Valutadatum,Product,ISIN,Omschrijving,FX,Mutatie,,Saldo,,Order Id
        // 01-10-2021,07:39,30-09-2021,,,Valuta Debitering,,EUR,"3,96",EUR,"36,38"
        // 01-10-2021,07:39,30-09-2021,,,Valuta Debitering,"1,1588",USD,"-4,60",USD,"0,00"
        // 30-09-2021,14:50,29-09-2021,VANGUARD S&P500,IE00B3XXRP09,Dividend,,USD,"4,60",USD,"4,60"
        $this->appendCsv(
            $date,
            $isn,
            $isn,
            'Dividend',
            $exchangeRate,
            $currency,
            $dividendReceived * $exchangeRate,
            'EUR',
            null,
            null,
        );
    }

    public function addDeposit(
        DateTimeInterface $date,
        string $currency,
        float $amount,
        ?float $exchangeRate = null,
    ): void {
        // Datum,Tijd,Valutadatum,Product,ISIN,Omschrijving,FX,Mutatie,,Saldo,,Order Id
        // 10-04-2021,10:05,10-04-2021,,,iDEAL storting,,EUR,"0,01",EUR,"0,01",
        $this->appendCsv(
            $date,
            null,
            null,
            'iDEAL storting',
            $exchangeRate,
            $currency,
            $amount,
            $currency,
            null,
            null,
        );
    }

    public function addWithdrawal(
        DateTimeInterface $date,
        string $currency,
        float $amount,
        ?float $exchangeRate = null,
    ): void {
        // TODO: I need an export of DEGIRO and IB that contains at least one money withdraw
    }

    public function addBrokerCost(
        DateTimeInterface $date,
        string $currency,
        float $amount,
        string $description = null,
        ?float $exchangeRate = null,
    ): void {
        // Datum,Tijd,Valutadatum,Product,ISIN,Omschrijving,FX,Mutatie,,Saldo,,Order Id
        // 02-07-2021,03:11,30-06-2021,,,Flatex Interest,,EUR,"-0,02",EUR,"28,99",
        $this->appendCsv(
            $date,
            null,
            null,
            $description,
            $exchangeRate,
            $currency,
            $amount,
            $currency,
            null,
            null,
        );
    }

    private function appendCsv(
        DateTimeInterface $date,
        ?string $product,
        ?string $isn,
        ?string $description,
        ?float $exchangeRate,
        ?string $mutationCurrency,
        ?float $mutationAmount,
        ?string $accountCurrency,
        ?float $accountAmount,
        ?string $orderId,
    ): void
    {
        $this->file->fseek(0, SEEK_END);
        $this->file->fputcsv([
            $date->format('d-m-Y'), // Datum,
            $date->format('H:i'), // Tijd,
            $date->format('d-m-Y'), // 'Valutadatum
            $product, // Product
            $isn,
            $description,
            $this->priceToString($exchangeRate),
            $mutationCurrency,
            $this->priceToString($mutationAmount),
            $accountCurrency,
            $this->priceToString($accountAmount),
            $orderId,
        ]);
    }

    private function priceToString(?float $price): ?string
    {
        if (!is_float($price)) {
            return null;
        }
        return number_format($price, 6, ',', '');
    }
}
