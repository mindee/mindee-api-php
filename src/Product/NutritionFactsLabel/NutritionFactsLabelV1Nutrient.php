<?php

namespace Mindee\Product\NutritionFactsLabel;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The amount of nutrients in the product.
 */
class NutritionFactsLabelV1Nutrient
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var float|null DVs are the recommended amounts of nutrients to consume or not to exceed each day.
     */
    public ?float $dailyValue;
    /**
     * @var string|null The name of nutrients of the product.
     */
    public ?string $name;
    /**
     * @var float|null The amount of nutrients per 100g of the product.
     */
    public ?float $per100G;
    /**
     * @var float|null The amount of nutrients per serving of the product.
     */
    public ?float $perServing;
    /**
     * @var string|null The unit of measurement for the amount of nutrients.
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
        $this->name = $rawPrediction["name"] ?? null;
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
        $outArr["dailyValue"] = SummaryHelper::formatFloat($this->dailyValue);
        $outArr["name"] = SummaryHelper::formatForDisplay($this->name, 20);
        $outArr["per100G"] = SummaryHelper::formatFloat($this->per100G);
        $outArr["perServing"] = SummaryHelper::formatFloat($this->perServing);
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
        $outArr["dailyValue"] = SummaryHelper::formatFloat($this->dailyValue);
        $outArr["name"] = SummaryHelper::formatForDisplay($this->name);
        $outArr["per100G"] = SummaryHelper::formatFloat($this->per100G);
        $outArr["perServing"] = SummaryHelper::formatFloat($this->perServing);
        $outArr["unit"] = SummaryHelper::formatForDisplay($this->unit);
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
        $outStr .= SummaryHelper::padString($printable["dailyValue"], 11);
        $outStr .= SummaryHelper::padString($printable["name"], 20);
        $outStr .= SummaryHelper::padString($printable["per100G"], 8);
        $outStr .= SummaryHelper::padString($printable["perServing"], 11);
        $outStr .= SummaryHelper::padString($printable["unit"], 4);
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
