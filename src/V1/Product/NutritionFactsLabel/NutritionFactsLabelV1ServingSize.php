<?php

declare(strict_types=1);

namespace Mindee\V1\Product\NutritionFactsLabel;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * The size of a single serving of the product.
 */
class NutritionFactsLabelV1ServingSize
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var float|null The amount of a single serving.
     */
    public ?float $amount;
    /**
     * @var string|null The unit for the amount of a single serving.
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
        $this->amount = isset($rawPrediction["amount"])
            ? (float) ($rawPrediction["amount"]) : null;
        $this->unit = $rawPrediction["unit"] ?? null;
        $this->pageId = $pageId;
    }

    /**
     * Return values for printing inside an RST table.
     *
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["amount"] = SummaryHelperV1::formatFloat($this->amount);
        $outArr["unit"] = SummaryHelperV1::formatForDisplay($this->unit);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     * @return array<string, string>
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["amount"] = SummaryHelperV1::formatFloat($this->amount);
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
        $outStr .= "\n  :Amount: " . $printable["amount"];
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
