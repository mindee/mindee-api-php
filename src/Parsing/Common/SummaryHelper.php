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
     * @param array  $column_sizes Sizes of the respective columns.
     * @param string $separator    Separator character.
     * @return string
     */
    public static function lineSeparator(array $column_sizes, string $separator): string
    {
        $out_str = "  +";
        foreach ($column_sizes as $size) {
            $out_str .= str_repeat($separator . "+", $size);
        }
        return $out_str;
    }

    /**
     * Truncates line-items to the max width of their corresponding column.
     *
     * @param string|null $input_string String to check.
     * @param int|null $max_col_size Maximum size for the current column, if it exists.
     * @return string
     */
    public static function formatForDisplay(?string $input_string = null, ?int $max_col_size = null): string
    {
        if (!$input_string || strlen($input_string) == 0) {
            return "";
        }
        if (!isset($max_col_size)) {
            return $input_string;
        }
        return count($input_string) < $max_col_size ? $input_string : substr($input_string, 0, $max_col_size - 3) . "...";
    }
}
