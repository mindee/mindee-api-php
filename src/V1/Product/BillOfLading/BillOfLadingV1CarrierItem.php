<?php

declare(strict_types=1);

namespace Mindee\V1\Product\BillOfLading;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The goods being shipped.
 */
class BillOfLadingV1CarrierItem
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null A description of the item.
     */
    public ?string $description;
    /**
     * @var float|null The gross weight of the item.
     */
    public ?float $grossWeight;
    /**
     * @var float|null The measurement of the item.
     */
    public ?float $measurement;
    /**
     * @var string|null The unit of measurement for the measurement.
     */
    public ?string $measurementUnit;
    /**
     * @var float|null The quantity of the item being shipped.
     */
    public ?float $quantity;
    /**
     * @var string|null The unit of measurement for weights.
     */
    public ?string $weightUnit;

    /**
     * @param array $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->description = $rawPrediction["description"] ?? null;
        $this->grossWeight = isset($rawPrediction["gross_weight"])
            ? (float) ($rawPrediction["gross_weight"]) : null;
        $this->measurement = isset($rawPrediction["measurement"])
            ? (float) ($rawPrediction["measurement"]) : null;
        $this->measurementUnit = $rawPrediction["measurement_unit"] ?? null;
        $this->quantity = isset($rawPrediction["quantity"])
            ? (float) ($rawPrediction["quantity"]) : null;
        $this->weightUnit = $rawPrediction["weight_unit"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["description"] = SummaryHelperV1::formatForDisplay($this->description, 36);
        $outArr["grossWeight"] = SummaryHelperV1::formatFloat($this->grossWeight);
        $outArr["measurement"] = SummaryHelperV1::formatFloat($this->measurement);
        $outArr["measurementUnit"] = SummaryHelperV1::formatForDisplay($this->measurementUnit);
        $outArr["quantity"] = SummaryHelperV1::formatFloat($this->quantity);
        $outArr["weightUnit"] = SummaryHelperV1::formatForDisplay($this->weightUnit);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     *
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["description"] = SummaryHelperV1::formatForDisplay($this->description);
        $outArr["grossWeight"] = SummaryHelperV1::formatFloat($this->grossWeight);
        $outArr["measurement"] = SummaryHelperV1::formatFloat($this->measurement);
        $outArr["measurementUnit"] = SummaryHelperV1::formatForDisplay($this->measurementUnit);
        $outArr["quantity"] = SummaryHelperV1::formatFloat($this->quantity);
        $outArr["weightUnit"] = SummaryHelperV1::formatForDisplay($this->weightUnit);
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
        $outStr .= SummaryHelperV1::padString($printable["description"], 36);
        $outStr .= SummaryHelperV1::padString($printable["grossWeight"], 12);
        $outStr .= SummaryHelperV1::padString($printable["measurement"], 11);
        $outStr .= SummaryHelperV1::padString($printable["measurementUnit"], 16);
        $outStr .= SummaryHelperV1::padString($printable["quantity"], 8);
        $outStr .= SummaryHelperV1::padString($printable["weightUnit"], 11);
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
