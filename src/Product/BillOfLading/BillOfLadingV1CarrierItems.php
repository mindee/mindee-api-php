<?php

namespace Mindee\Product\BillOfLading;

/**
 * The goods being shipped.
 */
class BillOfLadingV1CarrierItems extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new BillOfLadingV1CarrierItem($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function carrierItemsSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 38);
        $outStr .= "+" . str_repeat($char, 14);
        $outStr .= "+" . str_repeat($char, 13);
        $outStr .= "+" . str_repeat($char, 18);
        $outStr .= "+" . str_repeat($char, 10);
        $outStr .= "+" . str_repeat($char, 13);
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::carrierItemsSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::carrierItemsSeparator('-') . "\n ";
        $outStr .= " | Description                         ";
        $outStr .= " | Gross Weight";
        $outStr .= " | Measurement";
        $outStr .= " | Measurement Unit";
        $outStr .= " | Quantity";
        $outStr .= " | Weight Unit";
        $outStr .= " |\n" . self::carrierItemsSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
