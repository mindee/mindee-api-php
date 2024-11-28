<?php

namespace Mindee\Product\Fr\Payslip;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Information about the pay period.
 */
class PayslipV3PayPeriod
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null The end date of the pay period.
     */
    public ?string $endDate;
    /**
     * @var string|null The month of the pay period.
     */
    public ?string $month;
    /**
     * @var string|null The date of payment for the pay period.
     */
    public ?string $paymentDate;
    /**
     * @var string|null The start date of the pay period.
     */
    public ?string $startDate;
    /**
     * @var string|null The year of the pay period.
     */
    public ?string $year;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->endDate = $rawPrediction["end_date"] ?? null;
        $this->month = $rawPrediction["month"] ?? null;
        $this->paymentDate = $rawPrediction["payment_date"] ?? null;
        $this->startDate = $rawPrediction["start_date"] ?? null;
        $this->year = $rawPrediction["year"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["endDate"] = SummaryHelper::formatForDisplay($this->endDate);
        $outArr["month"] = SummaryHelper::formatForDisplay($this->month);
        $outArr["paymentDate"] = SummaryHelper::formatForDisplay($this->paymentDate);
        $outArr["startDate"] = SummaryHelper::formatForDisplay($this->startDate);
        $outArr["year"] = SummaryHelper::formatForDisplay($this->year);
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
        $outArr["endDate"] = SummaryHelper::formatForDisplay($this->endDate);
        $outArr["month"] = SummaryHelper::formatForDisplay($this->month);
        $outArr["paymentDate"] = SummaryHelper::formatForDisplay($this->paymentDate);
        $outArr["startDate"] = SummaryHelper::formatForDisplay($this->startDate);
        $outArr["year"] = SummaryHelper::formatForDisplay($this->year);
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
        $outStr .= "\n  :End Date: " . $printable["endDate"];
        $outStr .= "\n  :Month: " . $printable["month"];
        $outStr .= "\n  :Payment Date: " . $printable["paymentDate"];
        $outStr .= "\n  :Start Date: " . $printable["startDate"];
        $outStr .= "\n  :Year: " . $printable["year"];
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
