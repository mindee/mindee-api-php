<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\Payslip;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Information about paid time off.
 */
class PayslipV3PaidTimeOff
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

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
     * @var integer|null Page ID.
     */
    public ?int $pageId;

    /**
     * @param array<string, mixed> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->accrued = isset($rawPrediction["accrued"])
            ? (float) ($rawPrediction["accrued"]) : null;
        $this->period = $rawPrediction["period"] ?? null;
        $this->ptoType = $rawPrediction["pto_type"] ?? null;
        $this->remaining = isset($rawPrediction["remaining"])
            ? (float) ($rawPrediction["remaining"]) : null;
        $this->used = isset($rawPrediction["used"])
            ? (float) ($rawPrediction["used"]) : null;
        $this->pageId = $pageId;
    }

    /**
     * Return values for printing inside an RST table.
     * @return array<string, string>
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["accrued"] = SummaryHelperV1::formatFloat($this->accrued);
        $outArr["period"] = SummaryHelperV1::formatForDisplay($this->period, 6);
        $outArr["ptoType"] = SummaryHelperV1::formatForDisplay($this->ptoType, 11);
        $outArr["remaining"] = SummaryHelperV1::formatFloat($this->remaining);
        $outArr["used"] = SummaryHelperV1::formatFloat($this->used);
        return $outArr;
    }

    /**
     * Output in a format suitable for inclusion in an rST table.
     *
     */
    public function toTableLine(): string
    {
        $printable = $this->tablePrintableValues();
        $outStr = "| ";
        $outStr .= SummaryHelperV1::padString($printable["accrued"], 9);
        $outStr .= SummaryHelperV1::padString($printable["period"], 6);
        $outStr .= SummaryHelperV1::padString($printable["ptoType"], 11);
        $outStr .= SummaryHelperV1::padString($printable["remaining"], 9);
        $outStr .= SummaryHelperV1::padString($printable["used"], 9);
        return rtrim(SummaryHelperV1::cleanOutString($outStr));
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelperV1::cleanOutString($this->toTableLine());
    }
}
