<?php

namespace Mindee\Product\Fr\EnergyBill;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * Information about the energy meter.
 */
class EnergyBillV1MeterDetail
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null The unique identifier of the energy meter.
     */
    public ?string $meterNumber;
    /**
     * @var string|null The type of energy meter.
     */
    public ?string $meterType;
    /**
     * @var string|null The unit of power for energy consumption.
     */
    public ?string $unit;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->meterNumber = $rawPrediction["meter_number"] ?? null;
        $this->meterType = $rawPrediction["meter_type"] ?? null;
        $this->unit = $rawPrediction["unit"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["meterNumber"] = SummaryHelper::formatForDisplay($this->meterNumber);
        $outArr["meterType"] = SummaryHelper::formatForDisplay($this->meterType);
        $outArr["unit"] = SummaryHelper::formatForDisplay($this->unit);
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
        $outArr["meterNumber"] = SummaryHelper::formatForDisplay($this->meterNumber);
        $outArr["meterType"] = SummaryHelper::formatForDisplay($this->meterType);
        $outArr["unit"] = SummaryHelper::formatForDisplay($this->unit);
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
        $outStr .= "\n  :Meter Number: " . $printable["meterNumber"];
        $outStr .= "\n  :Meter Type: " . $printable["meterType"];
        $outStr .= "\n  :Unit of Power: " . $printable["unit"];
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
