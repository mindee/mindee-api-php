<?php

namespace Mindee\Product\NutritionFactsLabel;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The amount of saturated fat in the product.
 */
class NutritionFactsLabelV1SaturatedFat
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var float|null DVs are the recommended amounts of saturated fat to consume or not to exceed each day.
     */
    public ?float $dailyValue;
    /**
     * @var float|null The amount of saturated fat per 100g of the product.
     */
    public ?float $per100G;
    /**
     * @var float|null The amount of saturated fat per serving of the product.
     */
    public ?float $perServing;

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
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["dailyValue"] = SummaryHelper::formatFloat($this->dailyValue);
        $outArr["per100G"] = SummaryHelper::formatFloat($this->per100G);
        $outArr["perServing"] = SummaryHelper::formatFloat($this->perServing);
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
        $outArr["dailyValue"] = SummaryHelper::formatFloat($this->dailyValue);
        $outArr["per100G"] = SummaryHelper::formatFloat($this->per100G);
        $outArr["perServing"] = SummaryHelper::formatFloat($this->perServing);
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
