<?php

namespace Mindee\Product\Fr\Payslip;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Detailed information about the earnings.
 */
class PayslipV3SalaryDetail
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var float|null The amount of the earning.
     */
    public ?float $amount;
    /**
     * @var float|null The base rate value of the earning.
     */
    public ?float $base;
    /**
     * @var string|null The description of the earnings.
     */
    public ?string $description;
    /**
     * @var float|null The number of units in the earning.
     */
    public ?float $number;
    /**
     * @var float|null The rate of the earning.
     */
    public ?float $rate;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->amount = isset($rawPrediction["amount"]) ?
            floatval($rawPrediction["amount"]) : null;
        $this->base = isset($rawPrediction["base"]) ?
            floatval($rawPrediction["base"]) : null;
        $this->description = $rawPrediction["description"] ?? null;
        $this->number = isset($rawPrediction["number"]) ?
            floatval($rawPrediction["number"]) : null;
        $this->rate = isset($rawPrediction["rate"]) ?
            floatval($rawPrediction["rate"]) : null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["amount"] = SummaryHelper::formatFloat($this->amount);
        $outArr["base"] = SummaryHelper::formatFloat($this->base);
        $outArr["description"] = SummaryHelper::formatForDisplay($this->description, 36);
        $outArr["number"] = SummaryHelper::formatFloat($this->number);
        $outArr["rate"] = SummaryHelper::formatFloat($this->rate);
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
        $outArr["amount"] = SummaryHelper::formatFloat($this->amount);
        $outArr["base"] = SummaryHelper::formatFloat($this->base);
        $outArr["description"] = SummaryHelper::formatForDisplay($this->description);
        $outArr["number"] = SummaryHelper::formatFloat($this->number);
        $outArr["rate"] = SummaryHelper::formatFloat($this->rate);
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
        $outStr .= SummaryHelper::padString($printable["amount"], 12);
        $outStr .= SummaryHelper::padString($printable["base"], 9);
        $outStr .= SummaryHelper::padString($printable["description"], 36);
        $outStr .= SummaryHelper::padString($printable["number"], 6);
        $outStr .= SummaryHelper::padString($printable["rate"], 9);
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
