<?php

namespace Mindee\Product\Invoice;

/**
 * List of line item details.
 */
class InvoiceV4LineItems extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
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
        $outStr .= "+" . str_repeat($char, 17);
        $outStr .= "+" . str_repeat($char, 12);
        return $outStr . "+";
    }


    /**
     * String representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        $lines = "";
        $iterator = $this->getIterator();
        if (!$iterator->valid()) {
            return "";
        }
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
        $outStr .= " | Unit of measure";
        $outStr .= " | Unit Price";
        $outStr .= " |\n" . self::lineItemsSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
