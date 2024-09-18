<?php

namespace Mindee\Product\BillOfLading;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The goods being shipped.
 */
class BillOfLadingV1CarrierItem
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

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
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->description = $rawPrediction["description"] ?? null;
        $this->grossWeight = isset($rawPrediction["gross_weight"]) ?
            floatval($rawPrediction["gross_weight"]) : null;
        $this->measurement = isset($rawPrediction["measurement"]) ?
            floatval($rawPrediction["measurement"]) : null;
        $this->measurementUnit = $rawPrediction["measurement_unit"] ?? null;
        $this->quantity = isset($rawPrediction["quantity"]) ?
            floatval($rawPrediction["quantity"]) : null;
        $this->weightUnit = $rawPrediction["weight_unit"] ?? null;
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
        $outArr["grossWeight"] = SummaryHelper::formatFloat($this->grossWeight);
        $outArr["measurement"] = SummaryHelper::formatFloat($this->measurement);
        $outArr["measurementUnit"] = SummaryHelper::formatForDisplay($this->measurementUnit);
        $outArr["quantity"] = SummaryHelper::formatFloat($this->quantity);
        $outArr["weightUnit"] = SummaryHelper::formatForDisplay($this->weightUnit);
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
        $outArr["grossWeight"] = SummaryHelper::formatFloat($this->grossWeight);
        $outArr["measurement"] = SummaryHelper::formatFloat($this->measurement);
        $outArr["measurementUnit"] = SummaryHelper::formatForDisplay($this->measurementUnit);
        $outArr["quantity"] = SummaryHelper::formatFloat($this->quantity);
        $outArr["weightUnit"] = SummaryHelper::formatForDisplay($this->weightUnit);
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
        $outStr .= SummaryHelper::padString($printable["description"], 36);
        $outStr .= SummaryHelper::padString($printable["grossWeight"], 12);
        $outStr .= SummaryHelper::padString($printable["measurement"], 11);
        $outStr .= SummaryHelper::padString($printable["measurementUnit"], 16);
        $outStr .= SummaryHelper::padString($printable["quantity"], 8);
        $outStr .= SummaryHelper::padString($printable["weightUnit"], 11);
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
