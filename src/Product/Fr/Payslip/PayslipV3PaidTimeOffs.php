<?php

namespace Mindee\Product\Fr\Payslip;

/**
 * Information about paid time off.
 */
class PayslipV3PaidTimeOffs extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new PayslipV3PaidTimeOff($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function paidTimeOffSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 11);
        $outStr .= "+" . str_repeat($char, 8);
        $outStr .= "+" . str_repeat($char, 13);
        $outStr .= "+" . str_repeat($char, 11);
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::paidTimeOffSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::paidTimeOffSeparator('-') . "\n ";
        $outStr .= " | Accrued  ";
        $outStr .= " | Period";
        $outStr .= " | Type       ";
        $outStr .= " | Remaining";
        $outStr .= " | Used     ";
        $outStr .= " |\n" . self::paidTimeOffSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
