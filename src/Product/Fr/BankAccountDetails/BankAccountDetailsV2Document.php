<?php

namespace Mindee\Product\Fr\BankAccountDetails;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\StringField;

/**
 * Bank Account Details API version 2.0 document data.
 */
class BankAccountDetailsV2Document extends Prediction
{
    /**
     * @var StringField Full extraction of the account holders names.
     */
    public StringField $accountHoldersNames;
    /**
     * @var BankAccountDetailsV2Bban Full extraction of BBAN, including: branch code, bank code, account and key.
     */
    public BankAccountDetailsV2Bban $bban;
    /**
     * @var StringField Full extraction of the IBAN number.
     */
    public StringField $iban;
    /**
     * @var StringField Full extraction of the SWIFT code.
     */
    public StringField $swiftCode;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["account_holders_names"])) {
            throw new MindeeUnsetException();
        }
        $this->accountHoldersNames = new StringField(
            $rawPrediction["account_holders_names"],
            $pageId
        );
        if (!isset($rawPrediction["bban"])) {
            throw new MindeeUnsetException();
        }
        $this->bban = new BankAccountDetailsV2Bban(
            $rawPrediction["bban"],
            $pageId
        );
        if (!isset($rawPrediction["iban"])) {
            throw new MindeeUnsetException();
        }
        $this->iban = new StringField(
            $rawPrediction["iban"],
            $pageId
        );
        if (!isset($rawPrediction["swift_code"])) {
            throw new MindeeUnsetException();
        }
        $this->swiftCode = new StringField(
            $rawPrediction["swift_code"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $bbanToFieldList = $this->bban != null ? $this->bban->toFieldList() : "";

        $outStr = ":Account Holder's Names: $this->accountHoldersNames
:Basic Bank Account Number: $bbanToFieldList
:IBAN: $this->iban
:SWIFT Code: $this->swiftCode
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
