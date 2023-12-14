<?php

namespace Mindee\Product\Fr\BankAccountDetails;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for Bank Account Details, API version 1.
 */
class BankAccountDetailsV1Document extends Prediction
{
    /**
    * @var StringField|null The name of the account holder as seen on the document.
    */
    public ?StringField $accountHolderName;
    /**
    * @var StringField|null The International Bank Account Number (IBAN).
    */
    public ?StringField $iban;
    /**
    * @var StringField|null The bank's SWIFT Business Identifier Code (BIC).
    */
    public ?StringField $swift;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->accountHolderName = new StringField(
            $rawPrediction["account_holder_name"],
            $pageId
        );
        $this->iban = new StringField(
            $rawPrediction["iban"],
            $pageId
        );
        $this->swift = new StringField(
            $rawPrediction["swift"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        $outStr = ":IBAN: $this->iban
:Account Holder's Name: $this->accountHolderName
:SWIFT Code: $this->swift
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
