<?php

namespace Mindee\Parsing\Common;

/**
 * Utility class to handle information display.
 */
class SummaryHelper
{
    /**
     * @param float|null $number Number to parse.
     * @return string Number as a valid float string.
     */
    public static function formatFloat(?float $number): string
    {
        if ($number === null) {
            return '';
        }
        // First, format to 5 decimal places
        $formatted = number_format($number, 5, '.', '');

        // Trim trailing zeros, but keep at least 2 decimal places
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        // If less than 2 decimal places, pad with zeros
        if (substr_count($formatted, '.') == 0) {
            $formatted .= '.00';
        } elseif (mb_strlen($formatted) - strpos($formatted, '.') <= 2) {
            $formatted = sprintf("%.2f", $formatted);
        }

        return $formatted;
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
     * Prepends carriage return, new line & tab symbols with a backslash in a string.
     *
     * @param string|null $string The string to fix.
     * @return string|null The fixed string.
     */
    private static function escapeSpecialChars(?string $string): ?string
    {
        if ($string === null) {
            return null;
        }
        $find = array("\n", "\t", "\r");
        $replace = array("\\n", "\\t", "\\r");
        return str_replace($find, $replace, $string);
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
        $inputString = SummaryHelper::escapeSpecialChars($inputString);
        if (!$inputString || mb_strlen($inputString) == 0) {
            return "";
        }
        if (!isset($maxColSize)) {
            return $inputString;
        }
        return mb_strlen($inputString) <= $maxColSize ? $inputString : mb_substr(
            $inputString,
            0,
            $maxColSize - 3
        ) . "...";
    }
}
