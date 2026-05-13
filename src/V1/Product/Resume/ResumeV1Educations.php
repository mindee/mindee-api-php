<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Resume;

use ArrayObject;

/**
 * The list of the candidate's educational background.
 */
class ResumeV1Educations extends ArrayObject
{
    /**
     * @param array<string, mixed> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new ResumeV1Education($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     */
    public static function educationSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 17);
        $outStr .= "+" . str_repeat($char, 27);
        $outStr .= "+" . str_repeat($char, 11);
        $outStr .= "+" . str_repeat($char, 10);
        $outStr .= "+" . str_repeat($char, 27);
        $outStr .= "+" . str_repeat($char, 13);
        $outStr .= "+" . str_repeat($char, 12);
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::educationSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::educationSeparator('-') . "\n ";
        $outStr .= " | Domain         ";
        $outStr .= " | Degree                   ";
        $outStr .= " | End Month";
        $outStr .= " | End Year";
        $outStr .= " | School                   ";
        $outStr .= " | Start Month";
        $outStr .= " | Start Year";
        $outStr .= " |\n" . self::educationSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
