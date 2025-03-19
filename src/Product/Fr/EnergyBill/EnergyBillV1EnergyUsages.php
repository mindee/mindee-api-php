<?php

namespace Mindee\Product\Fr\EnergyBill;

/**
 * Details of energy consumption.
 */
class EnergyBillV1EnergyUsages extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new EnergyBillV1EnergyUsage($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function energyUsageSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 13);
        $outStr .= "+" . str_repeat($char, 38);
        $outStr .= "+" . str_repeat($char, 12);
        $outStr .= "+" . str_repeat($char, 12);
        $outStr .= "+" . str_repeat($char, 10);
        $outStr .= "+" . str_repeat($char, 11);
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::energyUsageSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::energyUsageSeparator('-') . "\n ";
        $outStr .= " | Consumption";
        $outStr .= " | Description                         ";
        $outStr .= " | End Date  ";
        $outStr .= " | Start Date";
        $outStr .= " | Tax Rate";
        $outStr .= " | Total    ";
        $outStr .= " | Unit of Measure";
        $outStr .= " | Unit Price";
        $outStr .= " |\n" . self::energyUsageSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
