<?php

namespace Mindee\Product\Fr\Payslip;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Information about paid time off.
 */
class PayslipV3PaidTimeOff
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var float|null The amount of paid time off accrued in the period.
     */
    public ?float $accrued;
    /**
     * @var string|null The paid time off period.
     */
    public ?string $period;
    /**
     * @var string|null The type of paid time off.
     */
    public ?string $ptoType;
    /**
     * @var float|null The remaining amount of paid time off at the end of the period.
     */
    public ?float $remaining;
    /**
     * @var float|null The amount of paid time off used in the period.
     */
    public ?float $used;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->accrued = isset($rawPrediction["accrued"]) ?
            floatval($rawPrediction["accrued"]) : null;
        $this->period = $rawPrediction["period"] ?? null;
        $this->ptoType = $rawPrediction["pto_type"] ?? null;
        $this->remaining = isset($rawPrediction["remaining"]) ?
            floatval($rawPrediction["remaining"]) : null;
        $this->used = isset($rawPrediction["used"]) ?
            floatval($rawPrediction["used"]) : null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["accrued"] = SummaryHelper::formatFloat($this->accrued);
        $outArr["period"] = SummaryHelper::formatForDisplay($this->period, 6);
        $outArr["ptoType"] = SummaryHelper::formatForDisplay($this->ptoType, 11);
        $outArr["remaining"] = SummaryHelper::formatFloat($this->remaining);
        $outArr["used"] = SummaryHelper::formatFloat($this->used);
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
        $outArr["accrued"] = SummaryHelper::formatFloat($this->accrued);
        $outArr["period"] = SummaryHelper::formatForDisplay($this->period);
        $outArr["ptoType"] = SummaryHelper::formatForDisplay($this->ptoType);
        $outArr["remaining"] = SummaryHelper::formatFloat($this->remaining);
        $outArr["used"] = SummaryHelper::formatFloat($this->used);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in an rST table.
     *
     * @return string
     */
    public function toTableLine(): string
    {
        $printable = $this->tablePrintableValues();
        $outStr = "| ";
        $outStr .= SummaryHelper::padString($printable["accrued"], 9);
        $outStr .= SummaryHelper::padString($printable["period"], 6);
        $outStr .= SummaryHelper::padString($printable["ptoType"], 11);
        $outStr .= SummaryHelper::padString($printable["remaining"], 9);
        $outStr .= SummaryHelper::padString($printable["used"], 9);
        return rtrim(SummaryHelper::cleanOutString($outStr));
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelper::cleanOutString($this->toTableLine());
    }
}
