<?php

declare(strict_types=1);

namespace Mindee\V1\Product\FinancialDocument;

use Mindee\V1\Parsing\Standard\FieldConfidenceMixin;
use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * List of line item present on the document.
 */
class FinancialDocumentV1LineItem
{
    use FieldConfidenceMixin;
    use FieldPositionMixin;

    /**
     * @var string|null The item description.
     */
    public ?string $description;
    /**
     * @var string|null The product code referring to the item.
     */
    public ?string $productCode;
    /**
     * @var float|null The item quantity
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
     * @var string|null The item unit of measure.
     */
    public ?string $unitMeasure;
    /**
     * @var float|null The item unit price.
     */
    public ?float $unitPrice;
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
        $this->description = $rawPrediction["description"] ?? null;
        $this->productCode = $rawPrediction["product_code"] ?? null;
        $this->quantity = isset($rawPrediction["quantity"])
            ? (float) ($rawPrediction["quantity"]) : null;
        $this->taxAmount = isset($rawPrediction["tax_amount"])
            ? (float) ($rawPrediction["tax_amount"]) : null;
        $this->taxRate = isset($rawPrediction["tax_rate"])
            ? (float) ($rawPrediction["tax_rate"]) : null;
        $this->totalAmount = isset($rawPrediction["total_amount"])
            ? (float) ($rawPrediction["total_amount"]) : null;
        $this->unitMeasure = $rawPrediction["unit_measure"] ?? null;
        $this->unitPrice = isset($rawPrediction["unit_price"])
            ? (float) ($rawPrediction["unit_price"]) : null;
        $this->pageId = $pageId;
    }

    /**
     * Return values for printing inside an RST table.
     *
     */
    private function tablePrintableValues(): array
    {
        $outArr = [];
        $outArr["description"] = SummaryHelperV1::formatForDisplay($this->description, 36);
        $outArr["productCode"] = SummaryHelperV1::formatForDisplay($this->productCode);
        $outArr["quantity"] = SummaryHelperV1::formatFloat($this->quantity);
        $outArr["taxAmount"] = SummaryHelperV1::formatFloat($this->taxAmount);
        $outArr["taxRate"] = SummaryHelperV1::formatFloat($this->taxRate);
        $outArr["totalAmount"] = SummaryHelperV1::formatFloat($this->totalAmount);
        $outArr["unitMeasure"] = SummaryHelperV1::formatForDisplay($this->unitMeasure);
        $outArr["unitPrice"] = SummaryHelperV1::formatFloat($this->unitPrice);
        return $outArr;
    }

    /**
     * Return values for printing as an array.
     * @return array<string, string>
     */
    private function printableValues(): array
    {
        $outArr = [];
        $outArr["description"] = SummaryHelperV1::formatForDisplay($this->description);
        $outArr["productCode"] = SummaryHelperV1::formatForDisplay($this->productCode);
        $outArr["quantity"] = SummaryHelperV1::formatFloat($this->quantity);
        $outArr["taxAmount"] = SummaryHelperV1::formatFloat($this->taxAmount);
        $outArr["taxRate"] = SummaryHelperV1::formatFloat($this->taxRate);
        $outArr["totalAmount"] = SummaryHelperV1::formatFloat($this->totalAmount);
        $outArr["unitMeasure"] = SummaryHelperV1::formatForDisplay($this->unitMeasure);
        $outArr["unitPrice"] = SummaryHelperV1::formatFloat($this->unitPrice);
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
        $outStr .= SummaryHelperV1::padString($printable["description"], 36);
        $outStr .= SummaryHelperV1::padString($printable["productCode"], 12);
        $outStr .= SummaryHelperV1::padString($printable["quantity"], 8);
        $outStr .= SummaryHelperV1::padString($printable["taxAmount"], 10);
        $outStr .= SummaryHelperV1::padString($printable["taxRate"], 12);
        $outStr .= SummaryHelperV1::padString($printable["totalAmount"], 12);
        $outStr .= SummaryHelperV1::padString($printable["unitMeasure"], 15);
        $outStr .= SummaryHelperV1::padString($printable["unitPrice"], 10);
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
