<?php

namespace Mindee\Product\Fr\EnergyBill;

/**
 * Details of Taxes and Contributions.
 */
class EnergyBillV1TaxesAndContributions extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new EnergyBillV1TaxesAndContribution($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function taxesAndContributionsSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 38);
        $outStr .= "+" . str_repeat($char, 12);
        $outStr .= "+" . str_repeat($char, 12);
        $outStr .= "+" . str_repeat($char, 10);
        $outStr .= "+" . str_repeat($char, 11);
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::taxesAndContributionsSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::taxesAndContributionsSeparator('-') . "\n ";
        $outStr .= " | Description                         ";
        $outStr .= " | End Date  ";
        $outStr .= " | Start Date";
        $outStr .= " | Tax Rate";
        $outStr .= " | Total    ";
        $outStr .= " | Unit Price";
        $outStr .= " |\n" . self::taxesAndContributionsSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
