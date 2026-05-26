<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Resume;

use ArrayObject;
use Stringable;

/**
 * The list of languages that the candidate is proficient in.
 * @extends ArrayObject<integer, ResumeV1Language>
 */
class ResumeV1Languages extends ArrayObject implements Stringable
{
    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new ResumeV1Language($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     */
    public static function languagesSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 10);
        $outStr .= "+" . str_repeat($char, 22);
        return $outStr . "+";
    }


    /**
     * String representation.
     *
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::languagesSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::languagesSeparator('-') . "\n ";
        $outStr .= " | Language";
        $outStr .= " | Level               ";
        $outStr .= " |\n" . self::languagesSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
