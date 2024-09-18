<?php

namespace Mindee\Product\Fr\EnergyBill;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Details of Taxes and Contributions.
 */
class EnergyBillV1TaxesAndContribution
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null Description or details of the Taxes and Contributions.
     */
    public ?string $description;
    /**
     * @var string|null The end date of the Taxes and Contributions.
     */
    public ?string $endDate;
    /**
     * @var string|null The start date of the Taxes and Contributions.
     */
    public ?string $startDate;
    /**
     * @var float|null The rate of tax applied to the total cost.
     */
    public ?float $taxRate;
    /**
     * @var float|null The total cost of Taxes and Contributions.
     */
    public ?float $total;
    /**
     * @var float|null The price per unit of Taxes and Contributions.
     */
    public ?float $unitPrice;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->description = $rawPrediction["description"] ?? null;
        $this->endDate = $rawPrediction["end_date"] ?? null;
        $this->startDate = $rawPrediction["start_date"] ?? null;
        $this->taxRate = isset($rawPrediction["tax_rate"]) ?
            floatval($rawPrediction["tax_rate"]) : null;
        $this->total = isset($rawPrediction["total"]) ?
            floatval($rawPrediction["total"]) : null;
        $this->unitPrice = isset($rawPrediction["unit_price"]) ?
            floatval($rawPrediction["unit_price"]) : null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["description"] = SummaryHelper::formatForDisplay($this->description, 36);
        $outArr["endDate"] = SummaryHelper::formatForDisplay($this->endDate, 10);
        $outArr["startDate"] = SummaryHelper::formatForDisplay($this->startDate);
        $outArr["taxRate"] = SummaryHelper::formatFloat($this->taxRate);
        $outArr["total"] = SummaryHelper::formatFloat($this->total);
        $outArr["unitPrice"] = SummaryHelper::formatFloat($this->unitPrice);
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
        $outArr["description"] = SummaryHelper::formatForDisplay($this->description);
        $outArr["endDate"] = SummaryHelper::formatForDisplay($this->endDate);
        $outArr["startDate"] = SummaryHelper::formatForDisplay($this->startDate);
        $outArr["taxRate"] = SummaryHelper::formatFloat($this->taxRate);
        $outArr["total"] = SummaryHelper::formatFloat($this->total);
        $outArr["unitPrice"] = SummaryHelper::formatFloat($this->unitPrice);
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
        $outStr .= mb_str_pad($printable["description"], 36, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["endDate"], 10, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["startDate"], 10, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["taxRate"], 8, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["total"], 9, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
        $outStr .= mb_str_pad($printable["unitPrice"], 10, ' ', STR_PAD_RIGHT, "UTF-8") . " | ";
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
