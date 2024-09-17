<?php

namespace Mindee\Product\Fr\Payslip;

/**
 * Detailed information about the earnings.
 */
class PayslipV2SalaryDetails extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new PayslipV2SalaryDetail($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function salaryDetailsSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 14);
        $outStr .= "+" . str_repeat($char, 11);
        $outStr .= "+" . str_repeat($char, 38);
        $outStr .= "+" . str_repeat($char, 11);
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::salaryDetailsSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::salaryDetailsSeparator('-') . "\n ";
        $outStr .= " | Amount      ";
        $outStr .= " | Base     ";
        $outStr .= " | Description                         ";
        $outStr .= " | Rate     ";
        $outStr .= " |\n" . self::salaryDetailsSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
