<?php

namespace Mindee\Product\Resume;

/**
 * The list of certificates obtained by the candidate.
 */
class ResumeV1Certificates extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new ResumeV1Certificate($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function certificatesSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 12);
        $outStr .= "+" . str_repeat($char, 32);
        $outStr .= "+" . str_repeat($char, 27);
        $outStr .= "+" . str_repeat($char, 6);
        return $outStr . "+";
    }


    /**
     * String representation.
     *
     * @return string
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::certificatesSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::certificatesSeparator('-') . "\n ";
        $outStr .= " | Grade     ";
        $outStr .= " | Name                          ";
        $outStr .= " | Provider                 ";
        $outStr .= " | Year";
        $outStr .= " |\n" . self::certificatesSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
