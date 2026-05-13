<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

use ArrayObject;

/**
 * List of tax lines information.
 * @extends ArrayObject<int, TaxField>
 */
class Taxes extends ArrayObject
{
    /**
     * @param array<string, mixed> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
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
     */
    private static function lineSeparator(string $char): string
    {
        $outStr = '  +' . str_repeat($char, 15);
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
        $outStr = "\n" . self::lineSeparator('-') . "\n";
        $outStr .= "  | Base          | Code   | Rate (%) | Amount        |\n";
        $outStr .= self::lineSeparator('=');
        $arr = [];
        $iterator = $this->getIterator();
        if (!$iterator->valid()) {
            return "";
        }
        while ($iterator->valid()) {
            $entry = $iterator->current();
            $arr[] = "\n  " . $entry->toTableLine() . "\n" . self::lineSeparator('-');
            $iterator->next();
        }
        $outStr .= implode("\n", $arr);

        return rtrim($outStr);
    }
}
