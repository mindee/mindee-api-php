<?php

namespace Mindee\Product\Us\BankCheck;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\AmountField;
use Mindee\Parsing\Standard\DateField;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for Bank Check, API version 1.
 */
class BankCheckV1Document extends Prediction
{
    /**
    * @var StringField The check payer's account number.
    */
    public StringField $accountNumber;
    /**
    * @var AmountField The amount of the check.
    */
    public AmountField $amount;
    /**
    * @var StringField The issuer's check number.
    */
    public StringField $checkNumber;
    /**
    * @var DateField The date the check was issued.
    */
    public DateField $date;
    /**
    * @var StringField[] List of the check's payees (recipients).
    */
    public array $payees;
    /**
    * @var StringField The check issuer's routing number.
    */
    public StringField $routingNumber;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->accountNumber = new StringField(
            $rawPrediction["account_number"],
            $pageId
        );
        $this->amount = new AmountField(
            $rawPrediction["amount"],
            $pageId
        );
        $this->checkNumber = new StringField(
            $rawPrediction["check_number"],
            $pageId
        );
        $this->date = new DateField(
            $rawPrediction["date"],
            $pageId
        );
        $this->payees = $rawPrediction["payees"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["payees"]
        );
        $this->routingNumber = new StringField(
            $rawPrediction["routing_number"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $payees = implode(
            "\n         ",
            $this->payees
        );

        $outStr = ":Check Issue Date: $this->date
:Amount: $this->amount
:Payees: $payees
:Routing Number: $this->routingNumber
:Account Number: $this->accountNumber
:Check Number: $this->checkNumber
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
