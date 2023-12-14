<?php

namespace Mindee\Product\Receipt;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * List of line item details.
 */
class ReceiptV5LineItem
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string The item description.
     */
    public ?string $description;
    /**
     * @var float The item quantity.
     */
    public ?float $quantity;
    /**
     * @var float The item total amount.
     */
    public ?float $totalAmount;
    /**
     * @var float The item unit price.
     */
    public ?float $unitPrice;

    /**
     * @param array        $rawPrediction Array containing the JSON document response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $this->setConfidence($rawPrediction);
        $this->setPosition($rawPrediction);
        $this->description = $rawPrediction["description"] ?? null;
        $this->quantity = isset($rawPrediction["quantity"]) ?
        number_format(floatval($rawPrediction["quantity"]), 2, ".", "") :
        null;
        $this->totalAmount = isset($rawPrediction["total_amount"]) ?
        number_format(floatval($rawPrediction["total_amount"]), 2, ".", "") :
        null;
        $this->unitPrice = isset($rawPrediction["unit_price"]) ?
        number_format(floatval($rawPrediction["unit_price"]), 2, ".", "") :
        null;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["description"] = SummaryHelper::formatForDisplay($this->description, 36);
        $outArr["quantity"] = $this->quantity == null ? "" : number_format($this->quantity, 2, ".", "");
        $outArr["totalAmount"] = $this->totalAmount == null ? "" : number_format($this->totalAmount, 2, ".", "");
        $outArr["unitPrice"] = $this->unitPrice == null ? "" : number_format($this->unitPrice, 2, ".", "");
        return $outArr;
    }
    /**
     * Output in a format suitable for inclusion in an rST table.
     *
     * @return string
     */
    public function toTableLine(): string
    {
        $printable = $this->printableValues();
        $outStr = "| ";
        $outStr .= str_pad($printable["description"], 36) . " | ";
        $outStr .= str_pad($printable["quantity"], 8) . " | ";
        $outStr .= str_pad($printable["totalAmount"], 12) . " | ";
        $outStr .= str_pad($printable["unitPrice"], 10) . " | ";
        return rtrim(SummaryHelper::cleanOutString($outStr));
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return SummaryHelper::cleanOutString($this->toFieldList());
    }
}
