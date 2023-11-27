<?php

namespace Mindee\Parsing\Standard;

/**
 * List of tax lines information.
 */
class Taxes extends \ArrayObject
{
    /**
     * @param array        $raw_prediction Raw prediction array.
     * @param integer|null $page_id        Page number for multi pages document.
     */
    public function __construct(array $raw_prediction, ?int $page_id)
    {
        $entries = [];
        foreach ($raw_prediction as $entry) {
            $entries[] = new TaxField($entry, $page_id);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a string of rst line separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    private static function lineSeparator(string $char): string
    {
        $out_str = '+' . str_repeat($char, 15);
        $out_str .= '+' . str_repeat($char, 8);
        $out_str .= '+' . str_repeat($char, 10);
        $out_str .= '+' . str_repeat($char, 15);

        return $out_str . '+';
    }

    /**
     * @return string String representation.
     */
    public function __toString()
    {
        $out_str = "\n" . Taxes::lineSeparator('-') . "\n";
        $out_str .= "  | Base          | Code   | Rate (%) | Amount        |\n";
        $out_str .= Taxes::lineSeparator('=');
        $arr = [];
        foreach ($this as $entry) {
            array_push($arr, "\n  " . $entry->toTableLine() . "\n" . Taxes::lineSeparator('='));
        }
        $out_str .= implode("\n", $arr);

        return $out_str;
    }
}
