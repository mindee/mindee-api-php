<?php

namespace Mindee\Product\Fr\BankAccountDetails;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\StringField;

/**
 * Bank Account Details API version 1.0 document data.
 */
class BankAccountDetailsV1Document extends Prediction
{
    /**
     * @var StringField The name of the account holder as seen on the document.
     */
    public StringField $accountHolderName;
    /**
     * @var StringField The International Bank Account Number (IBAN).
     */
    public StringField $iban;
    /**
     * @var StringField The bank's SWIFT Business Identifier Code (BIC).
     */
    public StringField $swift;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["account_holder_name"])) {
            throw new MindeeUnsetException();
        }
        $this->accountHolderName = new StringField(
            $rawPrediction["account_holder_name"],
            $pageId
        );
        if (!isset($rawPrediction["iban"])) {
            throw new MindeeUnsetException();
        }
        $this->iban = new StringField(
            $rawPrediction["iban"],
            $pageId
        );
        if (!isset($rawPrediction["swift"])) {
            throw new MindeeUnsetException();
        }
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
