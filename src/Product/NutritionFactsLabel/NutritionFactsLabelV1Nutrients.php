<?php

namespace Mindee\Product\NutritionFactsLabel;

/**
 * The amount of nutrients in the product.
 */
class NutritionFactsLabelV1Nutrients extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new NutritionFactsLabelV1Nutrient($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function nutrientsSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 13);
        $outStr .= "+" . str_repeat($char, 22);
        $outStr .= "+" . str_repeat($char, 10);
        $outStr .= "+" . str_repeat($char, 13);
        $outStr .= "+" . str_repeat($char, 6);
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::nutrientsSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::nutrientsSeparator('-') . "\n ";
        $outStr .= " | Daily Value";
        $outStr .= " | Name                ";
        $outStr .= " | Per 100g";
        $outStr .= " | Per Serving";
        $outStr .= " | Unit";
        $outStr .= " |\n" . self::nutrientsSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
