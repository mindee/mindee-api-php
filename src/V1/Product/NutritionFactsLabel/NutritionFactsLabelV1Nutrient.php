<?php

declare(strict_types=1);

namespace Mindee\V1\Product\NutritionFactsLabel;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The amount of nutrients in the product.
 */
class NutritionFactsLabelV1Nutrient
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

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
     * @param array $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->dailyValue = isset($rawPrediction["daily_value"])
            ? (float) ($rawPrediction["daily_value"]) : null;
        $this->name = $rawPrediction["name"] ?? null;
        $this->per100G = isset($rawPrediction["per_100g"])
            ? (float) ($rawPrediction["per_100g"]) : null;
        $this->perServing = isset($rawPrediction["per_serving"])
            ? (float) ($rawPrediction["per_serving"]) : null;
        $this->unit = $rawPrediction["unit"] ?? null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["dailyValue"] = SummaryHelperV1::formatFloat($this->dailyValue);
        $outArr["name"] = SummaryHelperV1::formatForDisplay($this->name, 20);
        $outArr["per100G"] = SummaryHelperV1::formatFloat($this->per100G);
        $outArr["perServing"] = SummaryHelperV1::formatFloat($this->perServing);
        $outArr["unit"] = SummaryHelperV1::formatForDisplay($this->unit);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     *
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["dailyValue"] = SummaryHelperV1::formatFloat($this->dailyValue);
        $outArr["name"] = SummaryHelperV1::formatForDisplay($this->name);
        $outArr["per100G"] = SummaryHelperV1::formatFloat($this->per100G);
        $outArr["perServing"] = SummaryHelperV1::formatFloat($this->perServing);
        $outArr["unit"] = SummaryHelperV1::formatForDisplay($this->unit);
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
        $outStr .= SummaryHelperV1::padString($printable["dailyValue"], 11);
        $outStr .= SummaryHelperV1::padString($printable["name"], 20);
        $outStr .= SummaryHelperV1::padString($printable["per100G"], 8);
        $outStr .= SummaryHelperV1::padString($printable["perServing"], 11);
        $outStr .= SummaryHelperV1::padString($printable["unit"], 4);
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
