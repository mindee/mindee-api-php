<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\EnergyBill;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;
use Stringable;

/**
 * Details of energy consumption.
 */
class EnergyBillV1EnergyUsage implements Stringable
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var float|null The price per unit of energy consumed.
     */
    public ?float $consumption;
    /**
     * @var string|null Description or details of the energy usage.
     */
    public ?string $description;
    /**
     * @var string|null The end date of the energy usage.
     */
    public ?string $endDate;
    /**
     * @var string|null The start date of the energy usage.
     */
    public ?string $startDate;
    /**
     * @var float|null The rate of tax applied to the total cost.
     */
    public ?float $taxRate;
    /**
     * @var float|null The total cost of energy consumed.
     */
    public ?float $total;
    /**
     * @var string|null The unit of measurement for energy consumption.
     */
    public ?string $unit;
    /**
     * @var float|null The price per unit of energy consumed.
     */
    public ?float $unitPrice;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, public ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->consumption = isset($rawPrediction["consumption"])
            ? (float) ($rawPrediction["consumption"]) : null;
        $this->description = $rawPrediction["description"] ?? null;
        $this->endDate = $rawPrediction["end_date"] ?? null;
        $this->startDate = $rawPrediction["start_date"] ?? null;
        $this->taxRate = isset($rawPrediction["tax_rate"])
            ? (float) ($rawPrediction["tax_rate"]) : null;
        $this->total = isset($rawPrediction["total"])
            ? (float) ($rawPrediction["total"]) : null;
        $this->unit = $rawPrediction["unit"] ?? null;
        $this->unitPrice = isset($rawPrediction["unit_price"])
            ? (float) ($rawPrediction["unit_price"]) : null;
    }

    /**
     * Return values for printing inside an RST table.
     * @return array<string, string>
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["consumption"] = SummaryHelperV1::formatFloat($this->consumption);
        $outArr["description"] = SummaryHelperV1::formatForDisplay($this->description, 36);
        $outArr["endDate"] = SummaryHelperV1::formatForDisplay($this->endDate, 10);
        $outArr["startDate"] = SummaryHelperV1::formatForDisplay($this->startDate);
        $outArr["taxRate"] = SummaryHelperV1::formatFloat($this->taxRate);
        $outArr["total"] = SummaryHelperV1::formatFloat($this->total);
        $outArr["unit"] = SummaryHelperV1::formatForDisplay($this->unit);
        $outArr["unitPrice"] = SummaryHelperV1::formatFloat($this->unitPrice);
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
        $outStr .= SummaryHelperV1::padString($printable["consumption"], 11);
        $outStr .= SummaryHelperV1::padString($printable["description"], 36);
        $outStr .= SummaryHelperV1::padString($printable["endDate"], 10);
        $outStr .= SummaryHelperV1::padString($printable["startDate"], 10);
        $outStr .= SummaryHelperV1::padString($printable["taxRate"], 8);
        $outStr .= SummaryHelperV1::padString($printable["total"], 9);
        $outStr .= SummaryHelperV1::padString($printable["unit"], 15);
        $outStr .= SummaryHelperV1::padString($printable["unitPrice"], 10);
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
