<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\Payslip;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Information about the pay period.
 */
class PayslipV3PayPeriod
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

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
     * @param array $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
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
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["endDate"] = SummaryHelperV1::formatForDisplay($this->endDate);
        $outArr["month"] = SummaryHelperV1::formatForDisplay($this->month);
        $outArr["paymentDate"] = SummaryHelperV1::formatForDisplay($this->paymentDate);
        $outArr["startDate"] = SummaryHelperV1::formatForDisplay($this->startDate);
        $outArr["year"] = SummaryHelperV1::formatForDisplay($this->year);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     *
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["endDate"] = SummaryHelperV1::formatForDisplay($this->endDate);
        $outArr["month"] = SummaryHelperV1::formatForDisplay($this->month);
        $outArr["paymentDate"] = SummaryHelperV1::formatForDisplay($this->paymentDate);
        $outArr["startDate"] = SummaryHelperV1::formatForDisplay($this->startDate);
        $outArr["year"] = SummaryHelperV1::formatForDisplay($this->year);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in a field list.
     *
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
        return SummaryHelperV1::cleanOutString($this->toFieldList());
    }
}
