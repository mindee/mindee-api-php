<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing;

use Mindee\Parsing\SummaryHelper;

use function function_exists;
use function strlen;

/**
 * Utility class to handle information display.
 */
class SummaryHelperV1 extends SummaryHelper
{
    /**
     * Pads and add separators to a string for rst table items.
     *
     * @param string $inputString Input value, as an already printable string.
     * @param integer $colSize Column size assigned to the value.
     * @param string $separator Optional custom separator for tables.
     * @return string The string, with table separators.
     */
    public static function padString(string $inputString, int $colSize, string $separator = "|"): string
    {
        if (function_exists('mb_str_pad')) {
            return mb_str_pad($inputString, $colSize, " ", STR_PAD_RIGHT, "UTF-8") . " $separator ";
        }

        $paddedLength = $colSize + (strlen($inputString) - mb_strlen($inputString, 'UTF-8'));
        return str_pad($inputString, $paddedLength, ' ', STR_PAD_RIGHT) . " $separator ";
    }
}
