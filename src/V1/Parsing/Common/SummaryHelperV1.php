<?php

namespace Mindee\V1\Parsing\Common;

use Mindee\Parsing\Common\SummaryHelper;

/**
 * Utility class to handle information display.
 */
class SummaryHelperV1 extends SummaryHelper
{
    /**
     * Pads and add separators to a string for rst table items.
     *
     * @param string  $inputString Input value, as an already printable string.
     * @param integer $colSize     Column size assigned to the value.
     * @param string  $separator   Optional custom separator for tables.
     * @return string The string, with table separators.
     */
    public static function padString(string $inputString, int $colSize, string $separator = "|"): string
    {
        return mb_str_pad($inputString, $colSize, " ", STR_PAD_RIGHT, "UTF-8") . " $separator ";
    }
}
