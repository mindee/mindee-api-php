<?php

namespace Mindee\Parsing\Common;

/**
 * Utility class to handle information display.
 */
class SummaryHelper
{
    /**
     * Formats decimal numbers for display, by removing all excess digits after the fifth.
     *
     * @param float|null $number Number to parse.
     * @return string Number as a valid float string.
     */
    public static function formatFloat(?float $number): string
    {
        if ($number === null) {
            return '';
        }
        $formatted = number_format($number, 5, '.', '');

        $formatted = rtrim(rtrim($formatted, '0'), '.');

        if (substr_count($formatted, '.') == 0) {
            $formatted .= '.00';
        } elseif (strlen($formatted) - strpos($formatted, '.') <= 2) {
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
        if (!$inputString || mb_strlen($inputString, "UTF-8") == 0) {
            return "";
        }
        if (!isset($maxColSize)) {
            return $inputString;
        }
        if (mb_strlen($inputString, "UTF-8") <= $maxColSize) {
            return $inputString;
        }
        return mb_substr(
            $inputString,
            0,
            $maxColSize - 3,
            "UTF-8"
        ) . "...";
    }

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
