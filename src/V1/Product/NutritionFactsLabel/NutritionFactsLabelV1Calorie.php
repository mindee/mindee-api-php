<?php

declare(strict_types=1);

namespace Mindee\V1\Product\NutritionFactsLabel;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The amount of calories in the product.
 */
class NutritionFactsLabelV1Calorie
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var float|null DVs are the recommended amounts of calories to consume or not to exceed each day.
     */
    public ?float $dailyValue;
    /**
     * @var float|null The amount of calories per 100g of the product.
     */
    public ?float $per100G;
    /**
     * @var float|null The amount of calories per serving of the product.
     */
    public ?float $perServing;
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
        $this->dailyValue = isset($rawPrediction["daily_value"])
            ? (float) ($rawPrediction["daily_value"]) : null;
        $this->per100G = isset($rawPrediction["per_100g"])
            ? (float) ($rawPrediction["per_100g"]) : null;
        $this->perServing = isset($rawPrediction["per_serving"])
            ? (float) ($rawPrediction["per_serving"]) : null;
        $this->pageId = $pageId;
    }

    /**
     * Return values for printing inside an RST table.
     *
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["dailyValue"] = SummaryHelperV1::formatFloat($this->dailyValue);
        $outArr["per100G"] = SummaryHelperV1::formatFloat($this->per100G);
        $outArr["perServing"] = SummaryHelperV1::formatFloat($this->perServing);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     * @return array<string, string>
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["dailyValue"] = SummaryHelperV1::formatFloat($this->dailyValue);
        $outArr["per100G"] = SummaryHelperV1::formatFloat($this->per100G);
        $outArr["perServing"] = SummaryHelperV1::formatFloat($this->perServing);
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
        return SummaryHelperV1::cleanOutString($this->toFieldList());
    }
}
