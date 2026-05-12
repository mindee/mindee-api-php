<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\Payslip;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Detailed information about the earnings.
 */
class PayslipV3SalaryDetail
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

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
     * @param array $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->amount = isset($rawPrediction["amount"])
            ? (float) ($rawPrediction["amount"]) : null;
        $this->base = isset($rawPrediction["base"])
            ? (float) ($rawPrediction["base"]) : null;
        $this->description = $rawPrediction["description"] ?? null;
        $this->number = isset($rawPrediction["number"])
            ? (float) ($rawPrediction["number"]) : null;
        $this->rate = isset($rawPrediction["rate"])
            ? (float) ($rawPrediction["rate"]) : null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["amount"] = SummaryHelperV1::formatFloat($this->amount);
        $outArr["base"] = SummaryHelperV1::formatFloat($this->base);
        $outArr["description"] = SummaryHelperV1::formatForDisplay($this->description, 36);
        $outArr["number"] = SummaryHelperV1::formatFloat($this->number);
        $outArr["rate"] = SummaryHelperV1::formatFloat($this->rate);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     *
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["amount"] = SummaryHelperV1::formatFloat($this->amount);
        $outArr["base"] = SummaryHelperV1::formatFloat($this->base);
        $outArr["description"] = SummaryHelperV1::formatForDisplay($this->description);
        $outArr["number"] = SummaryHelperV1::formatFloat($this->number);
        $outArr["rate"] = SummaryHelperV1::formatFloat($this->rate);
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
        $outStr .= SummaryHelperV1::padString($printable["amount"], 12);
        $outStr .= SummaryHelperV1::padString($printable["base"], 9);
        $outStr .= SummaryHelperV1::padString($printable["description"], 36);
        $outStr .= SummaryHelperV1::padString($printable["number"], 6);
        $outStr .= SummaryHelperV1::padString($printable["rate"], 9);
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
