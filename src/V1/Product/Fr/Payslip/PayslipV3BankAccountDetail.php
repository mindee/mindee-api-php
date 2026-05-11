<?php

namespace Mindee\V1\Product\Fr\Payslip;

use Mindee\V1\Parsing\Common\SummaryHelperV1;
use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;

/**
 * Information about the employee's bank account.
 */
class PayslipV3BankAccountDetail
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null The name of the bank.
     */
    public ?string $bankName;
    /**
     * @var string|null The IBAN of the bank account.
     */
    public ?string $iban;
    /**
     * @var string|null The SWIFT code of the bank.
     */
    public ?string $swift;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->bankName = $rawPrediction["bank_name"] ?? null;
        $this->iban = $rawPrediction["iban"] ?? null;
        $this->swift = $rawPrediction["swift"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["bankName"] = SummaryHelperV1::formatForDisplay($this->bankName);
        $outArr["iban"] = SummaryHelperV1::formatForDisplay($this->iban);
        $outArr["swift"] = SummaryHelperV1::formatForDisplay($this->swift);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["bankName"] = SummaryHelperV1::formatForDisplay($this->bankName);
        $outArr["iban"] = SummaryHelperV1::formatForDisplay($this->iban);
        $outArr["swift"] = SummaryHelperV1::formatForDisplay($this->swift);
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
        $outStr .= "\n  :Bank Name: " . $printable["bankName"];
        $outStr .= "\n  :IBAN: " . $printable["iban"];
        $outStr .= "\n  :SWIFT: " . $printable["swift"];
        return rtrim($outStr);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelperV1::cleanOutString($this->toFieldList());
    }
}
