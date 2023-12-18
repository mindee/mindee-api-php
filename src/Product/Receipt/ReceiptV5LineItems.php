<?php

namespace Mindee\Product\Receipt;

/**
 * List of line item details.
 */
class ReceiptV5LineItems extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new ReceiptV5LineItem($entry, $pageId);
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
        $outStr .= "+" . str_repeat($char, 10);
        $outStr .= "+" . str_repeat($char, 14);
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
        $outStr .= " | Quantity";
        $outStr .= " | Total Amount";
        $outStr .= " | Unit Price";
        $outStr .= " |\n" . self::lineItemsSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
