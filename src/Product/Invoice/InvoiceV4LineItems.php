<?php

namespace Mindee\Product\Invoice;

/**
* List of line items for InvoiceV4.
 */
class InvoiceV4LineItems extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages PDF.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction["line_items"] as $entry) {
            $entries[] = new InvoiceV4LineItem($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function lineItemsSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 38);
        $outStr .= "+" . str_repeat($char, 14);
        $outStr .= "+" . str_repeat($char, 10);
        $outStr .= "+" . str_repeat($char, 12);
        $outStr .= "+" . str_repeat($char, 14);
        $outStr .= "+" . str_repeat($char, 14);
        $outStr .= "+" . str_repeat($char, 12);
        return $outStr . "+";
    }

    /**
     * String representation for line items.
     *
     * @return string
     */
    public function lineItemsToStr(): string
    {
        if (!$this->invoiceV4Document->lineItems || count($this->invoiceV4Document->lineItems) == 0) {
            return "";
        }

        $lines = "";
        $iterator = $this->getIterator();
        while ($iterator->valid()) {
            $entry = $iterator->current();
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::lineItemsSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::lineItemsSeparator('-') . "\n ";
        $outStr .= " | Description                         ";
        $outStr .= " | Product code";
        $outStr .= " | Quantity";
        $outStr .= " | Tax Amount";
        $outStr .= " | Tax Rate (%)";
        $outStr .= " | Total Amount";
        $outStr .= " | Unit Price";
        $outStr .= " |\n" . self::lineItemsSeparator("=");
        $outStr .= "\n  $lines";
        $outStr .= " |\n" . self::lineItemsSeparator("-");
        return $outStr;
    }
}
