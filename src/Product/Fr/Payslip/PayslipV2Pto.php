<?php

namespace Mindee\Product\Fr\Payslip;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Information about paid time off.
 */
class PayslipV2Pto
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var float|null The amount of paid time off accrued in this period.
     */
    public ?float $accruedThisPeriod;
    /**
     * @var float|null The balance of paid time off at the end of the period.
     */
    public ?float $balanceEndOfPeriod;
    /**
     * @var float|null The amount of paid time off used in this period.
     */
    public ?float $usedThisPeriod;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->accruedThisPeriod = isset($rawPrediction["accrued_this_period"]) ?
            floatval($rawPrediction["accrued_this_period"]) : null;
        $this->balanceEndOfPeriod = isset($rawPrediction["balance_end_of_period"]) ?
            floatval($rawPrediction["balance_end_of_period"]) : null;
        $this->usedThisPeriod = isset($rawPrediction["used_this_period"]) ?
            floatval($rawPrediction["used_this_period"]) : null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["accruedThisPeriod"] = SummaryHelper::formatFloat($this->accruedThisPeriod);
        $outArr["balanceEndOfPeriod"] = SummaryHelper::formatFloat($this->balanceEndOfPeriod);
        $outArr["usedThisPeriod"] = SummaryHelper::formatFloat($this->usedThisPeriod);
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
        $outArr["accruedThisPeriod"] = SummaryHelper::formatFloat($this->accruedThisPeriod);
        $outArr["balanceEndOfPeriod"] = SummaryHelper::formatFloat($this->balanceEndOfPeriod);
        $outArr["usedThisPeriod"] = SummaryHelper::formatFloat($this->usedThisPeriod);
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
        $outStr .= "\n  :Accrued This Period: " . $printable["accruedThisPeriod"];
        $outStr .= "\n  :Balance End of Period: " . $printable["balanceEndOfPeriod"];
        $outStr .= "\n  :Used This Period: " . $printable["usedThisPeriod"];
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
