<?php

namespace Mindee\Parsing\Common;

/**
 * Utility class to handle information display.
 */
class SummaryHelper
{
    /**
     * Adds custom separators for console display in line-items-like fields.
     *
     * @param array  $columnSizes Sizes of the respective columns.
     * @param string $separator   Separator character.
     * @return string
     */
    public static function lineSeparator(array $columnSizes, string $separator): string
    {
        $outStr = "  +";
        foreach ($columnSizes as $size) {
            $outStr .= str_repeat($separator . "+", $size);
        }
        return $outStr;
    }

    /**
     * Properly formats carriage returns in a string and remove leading spaces before them.
     *
     * @param string $inputString String to clean.
     * @return string
     */
    public static function cleanOutString(string $inputString): string
    {
        return preg_replace('/ *([\n\r])/', "\n", $inputString);
    }

    /**
     * Truncates line-items to the max width of their corresponding column.
     *
     * @param string|boolean|null $inputString String to check.
     * @param integer|null        $maxColSize  Maximum size for the current column, if it exists.
     * @return string
     */
    public static function formatForDisplay($inputString = null, ?int $maxColSize = null): string
    {
        if ($inputString === true) {
            return 'True';
        }
        if ($inputString === false) {
            return 'False';
        }
        if (!$inputString || strlen($inputString) == 0) {
            return "";
        }
        if (!isset($maxColSize)) {
            return $inputString;
        }
        return strlen($inputString) <= $maxColSize ? $inputString : substr(
            $inputString,
            0,
            $maxColSize - 3
        ) . "...";
    }
}
