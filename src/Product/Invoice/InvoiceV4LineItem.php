<?php

namespace Mindee\Product\Invoice;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * List of line item details.
 */
class InvoiceV4LineItem
{
    use FieldPositionMixin;
    use FieldConfidenceMixin;

    /**
     * @var string|null The item description.
     */
    public ?string $description;
    /**
     * @var string|null The product code referring to the item.
     */
    public ?string $productCode;
    /**
     * @var float|null The item quantity.
     */
    public ?float $quantity;
    /**
     * @var float|null The item tax amount.
     */
    public ?float $taxAmount;
    /**
     * @var float|null The item tax rate in percentage.
     */
    public ?float $taxRate;
    /**
     * @var float|null The item total amount.
     */
    public ?float $totalAmount;
    /**
     * @var float|null The item unit price.
     */
    public ?float $unitPrice;
    /**
     * @var integer The document page on which the information was found.
     */
    public ?int $pageN;

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
        $this->pageN = $pageId;

        $this->description = $rawPrediction["description"];
        $this->productCode = $rawPrediction["product_code"];
        $this->quantity = floatval($rawPrediction["quantity"]);
        $this->taxAmount = floatval($rawPrediction["tax_amount"]);
        $this->taxRate = floatval($rawPrediction["tax_rate"]);
        $this->totalAmount = floatval($rawPrediction["total_amount"]);
        $this->unitPrice = floatval($rawPrediction["unit_price"]);
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
        $outArr["productCode"] = SummaryHelper::formatForDisplay($this->productCode, null);
        $outArr["quantity"] = SummaryHelper::formatForDisplay($this->quantity);
        $outArr["taxAmount"] = SummaryHelper::formatForDisplay($this->taxAmount);
        $outArr["taxRate"] = SummaryHelper::formatForDisplay($this->taxRate);
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
        $outStr .= str_pad($printable["description"], 36) . " | ";
        $outStr .= str_pad($printable["productCode"], 12) . " | ";
        $outStr .= str_pad($printable["quantity"], 8) . " | ";
        $outStr .= str_pad($printable["taxAmount"], 10) . " | ";
        $outStr .= str_pad($printable["taxRate"], 12) . " | ";
        $outStr .= str_pad($printable["totalAmount"], 12) . " | ";
        $outStr .= str_pad($printable["unitPrice"], 10) . " | ";
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
