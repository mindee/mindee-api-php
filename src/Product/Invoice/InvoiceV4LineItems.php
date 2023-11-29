<?php

namespace Mindee\Product\Invoice;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\FieldConfidenceMixin;
use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * List of line item details.
 */
class InvoiceV4LineItems
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
     * @var integer The document page on which the information was found..
     */
    public ?int $pageN;

    /**
     * @param array        $raw_prediction Array containing the JSON document response.
     * @param integer|null $page_id        Page number for multi pages PDF.
     */
    public function __construct(array $raw_prediction, ?int $page_id)
    {
        $this->setConfidence($raw_prediction);
        $this->setPosition($raw_prediction);

        if (!isset($page_id)) {
            if (array_key_exists("page_id", $raw_prediction)) {
                $page_id = $raw_prediction["page_id"];
            }
        }
        $this->pageN = $page_id;

        $this->description = $raw_prediction["description"];
        $this->productCode = $raw_prediction["product_code"];
        $this->quantity = floatval($raw_prediction["quantity"]);
        $this->taxAmount = floatval($raw_prediction["tax_amount"]);
        $this->taxRate = floatval($raw_prediction["tax_rate"]);
        $this->totalAmount = floatval($raw_prediction["total_amount"]);
        $this->unitPrice = floatval($raw_prediction["unit_price"]);
    }

    /**
     * Return values for printing as an array.
     *
     * @return array
     */
    private function printableValues(): array
    {
        $out_arr = [];
        $out_arr["description"] = SummaryHelper::formatForDisplay($this->description, 36);
        $out_arr["product_code"] = SummaryHelper::formatForDisplay($this->productCode, null);
        $out_arr["quantity"] = SummaryHelper::formatForDisplay($this->quantity);
        $out_arr["tax_amount"] = SummaryHelper::formatForDisplay($this->taxAmount);
        $out_arr["tax_rate"] = SummaryHelper::formatForDisplay($this->taxRate);
        $out_arr["total_amount"] = SummaryHelper::formatForDisplay($this->totalAmount);
        $out_arr["unit_price"] = SummaryHelper::formatForDisplay($this->unitPrice);
        return $out_arr;
    }

    /**
     * Output in a format suitable for inclusion in an rST table.
     *
     * @return string
     */
    private function toTableLines(): string
    {
        $printable = $this->printableValues();
        $out_str = "| " . str_pad($printable["description"], 36) . " | ";
        $out_str .= str_pad($printable["product_code"], 12);
        $out_str .= str_pad($printable["quantity"], 8);
        $out_str .= str_pad($printable["tax_amount"], 10);
        $out_str .= str_pad($printable["tax_rate"], 12);
        $out_str .= str_pad($printable["total_amount"], 12);
        $out_str .= str_pad($printable["unit_price"], 10);
        return SummaryHelper::cleanOutString($out_str);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $out_str = ":Page indexes: ";
        $out_str .= implode(", ", $this->pageIndexes);
        return trim($out_str);
    }
}
