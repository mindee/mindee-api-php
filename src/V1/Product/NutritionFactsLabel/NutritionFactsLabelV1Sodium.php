<?php

namespace Mindee\V1\Product\NutritionFactsLabel;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The amount of sodium in the product.
 */
class NutritionFactsLabelV1Sodium
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var float|null DVs are the recommended amounts of sodium to consume or not to exceed each day.
     */
    public ?float $dailyValue;
    /**
     * @var float|null The amount of sodium per 100g of the product.
     */
    public ?float $per100G;
    /**
     * @var float|null The amount of sodium per serving of the product.
     */
    public ?float $perServing;
    /**
     * @var string|null The unit of measurement for the amount of sodium.
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
        $this->dailyValue = isset($rawPrediction["daily_value"]) ?
            floatval($rawPrediction["daily_value"]) : null;
        $this->per100G = isset($rawPrediction["per_100g"]) ?
            floatval($rawPrediction["per_100g"]) : null;
        $this->perServing = isset($rawPrediction["per_serving"]) ?
            floatval($rawPrediction["per_serving"]) : null;
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
        $outArr["dailyValue"] = SummaryHelperV1::formatFloat($this->dailyValue);
        $outArr["per100G"] = SummaryHelperV1::formatFloat($this->per100G);
        $outArr["perServing"] = SummaryHelperV1::formatFloat($this->perServing);
        $outArr["unit"] = SummaryHelperV1::formatForDisplay($this->unit);
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
        $outArr["dailyValue"] = SummaryHelperV1::formatFloat($this->dailyValue);
        $outArr["per100G"] = SummaryHelperV1::formatFloat($this->per100G);
        $outArr["perServing"] = SummaryHelperV1::formatFloat($this->perServing);
        $outArr["unit"] = SummaryHelperV1::formatForDisplay($this->unit);
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
        $outStr .= "\n  :Daily Value: " . $printable["dailyValue"];
        $outStr .= "\n  :Per 100g: " . $printable["per100G"];
        $outStr .= "\n  :Per Serving: " . $printable["perServing"];
        $outStr .= "\n  :Unit: " . $printable["unit"];
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
