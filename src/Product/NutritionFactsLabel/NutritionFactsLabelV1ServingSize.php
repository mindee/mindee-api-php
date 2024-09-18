<?php

namespace Mindee\Product\NutritionFactsLabel;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * The size of a single serving of the product.
 */
class NutritionFactsLabelV1ServingSize
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var float|null The amount of a single serving.
     */
    public ?float $amount;
    /**
     * @var string|null The unit for the amount of a single serving.
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
        $this->amount = isset($rawPrediction["amount"]) ?
            floatval($rawPrediction["amount"]) : null;
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
        $outArr["amount"] = SummaryHelper::formatFloat($this->amount);
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
        $outArr["amount"] = SummaryHelper::formatFloat($this->amount);
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
        $outStr .= "\n  :Amount: " . $printable["amount"];
        $outStr .= "\n  :Unit: " . $printable["unit"];
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
