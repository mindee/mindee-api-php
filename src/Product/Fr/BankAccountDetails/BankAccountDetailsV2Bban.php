<?php

namespace Mindee\Product\Fr\BankAccountDetails;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Full extraction of BBAN, including: branch code, bank code, account and key.
 */
class BankAccountDetailsV2Bban
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
    * @var string|null The BBAN bank code outputted as a string.
    */
    public ?string $bbanBankCode;
    /**
    * @var string|null The BBAN branch code outputted as a string.
    */
    public ?string $bbanBranchCode;
    /**
    * @var string|null The BBAN key outputted as a string.
    */
    public ?string $bbanKey;
    /**
    * @var string|null The BBAN Account number outputted as a string.
    */
    public ?string $bbanNumber;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);

        if (!isset($pageId)) {
            if (array_key_exists("page_id", $rawPrediction)) {
                $pageId = $rawPrediction["page_id"];
            }
        }
        $this->bbanBankCode = $rawPrediction["bban_bank_code"];
        $this->bbanBranchCode = $rawPrediction["bban_branch_code"];
        $this->bbanKey = $rawPrediction["bban_key"];
        $this->bbanNumber = $rawPrediction["bban_number"];
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["bbanBankCode"] = SummaryHelper::formatForDisplay($this->bbanBankCode);
        $outArr["bbanBranchCode"] = SummaryHelper::formatForDisplay($this->bbanBranchCode);
        $outArr["bbanKey"] = SummaryHelper::formatForDisplay($this->bbanKey);
        $outArr["bbanNumber"] = SummaryHelper::formatForDisplay($this->bbanNumber);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in a field list.
     *
     * @return string
     */
    public function toFieldList(): string
    {
        $printable = $this->printableValues();
        $outStr = "";
        $outStr .= "\n  :Bank Code: " . $printable["bbanBankCode"];
        $outStr .= "\n  :Branch Code: " . $printable["bbanBranchCode"];
        $outStr .= "\n  :Key: " . $printable["bbanKey"];
        $outStr .= "\n  :Account Number: " . $printable["bbanNumber"];
        return rtrim($outStr);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelper::cleanOutString($this->toFieldList());
    }
}
