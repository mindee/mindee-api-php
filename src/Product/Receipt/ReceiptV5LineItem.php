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
            floatval($rawPrediction["quantity"]) : null;
        $this->totalAmount = isset($rawPrediction["total_amount"]) ?
            floatval($rawPrediction["total_amount"]) : null;
        $this->unitPrice = isset($rawPrediction["unit_price"]) ?
            floatval($rawPrediction["unit_price"]) : null;
    }

    /**
     * Return values for printing inside an RST table.
     *
     * @return array
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["description"] = SummaryHelper::formatForDisplay($this->description, 36);
        $outArr["quantity"] = SummaryHelper::formatFloat($this->quantity);
        $outArr["totalAmount"] = SummaryHelper::formatFloat($this->totalAmount);
        $outArr["unitPrice"] = SummaryHelper::formatFloat($this->unitPrice);
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
        $outArr["description"] = SummaryHelper::formatForDisplay($this->description);
        $outArr["quantity"] = SummaryHelper::formatFloat($this->quantity);
        $outArr["totalAmount"] = SummaryHelper::formatFloat($this->totalAmount);
        $outArr["unitPrice"] = SummaryHelper::formatFloat($this->unitPrice);
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
        $outStr .= mb_str_pad($printable["description"], 36) . " | ";
        $outStr .= mb_str_pad($printable["quantity"], 8) . " | ";
        $outStr .= mb_str_pad($printable["totalAmount"], 12) . " | ";
        $outStr .= mb_str_pad($printable["unitPrice"], 10) . " | ";
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
