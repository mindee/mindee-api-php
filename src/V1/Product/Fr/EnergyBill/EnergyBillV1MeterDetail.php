<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Fr\EnergyBill;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Information about the energy meter.
 */
class EnergyBillV1MeterDetail
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

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
        $this->meterNumber = $rawPrediction["meter_number"] ?? null;
        $this->meterType = $rawPrediction["meter_type"] ?? null;
        $this->unit = $rawPrediction["unit"] ?? null;
        $this->pageId = $pageId;
    }

    /**
     * Return values for printing as an array.
     * @return array<string, string>
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["meterNumber"] = SummaryHelperV1::formatForDisplay($this->meterNumber);
        $outArr["meterType"] = SummaryHelperV1::formatForDisplay($this->meterType);
        $outArr["unit"] = SummaryHelperV1::formatForDisplay($this->unit);
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in a field list.
     *
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
        return SummaryHelperV1::cleanOutString($this->toFieldList());
    }
}
