<?php

namespace Mindee\Product\Fr\BankAccountDetails;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for Bank Account Details, API version 2.
 */
class BankAccountDetailsV2Document extends Prediction
{
    /**
    * @var StringField|null Full extraction of the account holders names.
    */
    public ?StringField $accountHoldersNames;
    /**
    * @var BankAccountDetailsV2Bban|null Full extraction of BBAN, including: branch code, bank code, account and key.
    */
    public ?BankAccountDetailsV2Bban $bban;
    /**
    * @var StringField|null Full extraction of the IBAN number.
    */
    public ?StringField $iban;
    /**
    * @var StringField|null Full extraction of the SWIFT code.
    */
    public ?StringField $swiftCode;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->accountHoldersNames = new StringField(
            $rawPrediction["account_holders_names"],
            $pageId
        );
        $this->bban = new BankAccountDetailsV2Bban(
            $rawPrediction["bban"],
            $pageId
        );
        $this->iban = new StringField(
            $rawPrediction["iban"],
            $pageId
        );
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
