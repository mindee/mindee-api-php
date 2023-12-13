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
    * @var string|null The item description.
    */
    public ?string $description;
    /**
    * @var float|null The item quantity.
    */
    public ?float $quantity;
    /**
    * @var float|null The item total amount.
    */
    public ?float $totalAmount;
    /**
    * @var float|null The item unit price.
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

        if (!isset($pageId)) {
            if (array_key_exists("page_id", $rawPrediction)) {
                $pageId = $rawPrediction["page_id"];
            }
        }
        $this->description = $rawPrediction["description"];
        $this->quantity = isset($rawPrediction["quantity"]) ? floatval($rawPrediction["quantity"]) : null;
        $this->totalAmount = isset($rawPrediction["total_amount"]) ? floatval($rawPrediction["total_amount"]) : null;
        $this->unitPrice = isset($rawPrediction["unit_price"]) ? floatval($rawPrediction["unit_price"]) : null;
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["description"] = SummaryHelper::formatForDisplay($this->description, 25);
        $outArr["quantity"] = SummaryHelper::formatForDisplay($this->quantity);
        $outArr["totalAmount"] = SummaryHelper::formatForDisplay($this->totalAmount);
        $outArr["unitPrice"] = SummaryHelper::formatForDisplay($this->unitPrice);
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
        $outStr .= str_pad($printable["description"], 25) . " | ";
        $outStr .= str_pad($printable["quantity"], 0) . " | ";
        $outStr .= str_pad($printable["totalAmount"], 0) . " | ";
        $outStr .= str_pad($printable["unitPrice"], 0) . " | ";
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
