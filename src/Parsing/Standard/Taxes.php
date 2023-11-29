<?php

namespace Mindee\Parsing\Standard;

/**
 * List of tax lines information.
 */
class Taxes extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new TaxField($entry, $pageId);
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
        $outStr = '+' . str_repeat($char, 15);
        $outStr .= '+' . str_repeat($char, 8);
        $outStr .= '+' . str_repeat($char, 10);
        $outStr .= '+' . str_repeat($char, 15);

        return $outStr . '+';
    }

    /**
     * @return string String representation.
     */
    public function __toString()
    {
        $outStr = "\n" . Taxes::lineSeparator('-') . "\n";
        $outStr .= "  | Base          | Code   | Rate (%) | Amount        |\n";
        $outStr .= Taxes::lineSeparator('=');
        $arr = [];
        foreach ($this as $entry) {
            array_push($arr, "\n  " . $entry->toTableLine() . "\n" . Taxes::lineSeparator('='));
        }
        $outStr .= implode("\n", $arr);

        return $outStr;
    }
}
