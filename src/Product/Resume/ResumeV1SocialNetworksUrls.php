<?php

namespace Mindee\Product\Resume;

/**
 * The list of social network profiles of the candidate.
 */
class ResumeV1SocialNetworksUrls extends \ArrayObject
{
    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $entries = [];
        foreach ($rawPrediction as $entry) {
            $entries[] = new ResumeV1SocialNetworksUrl($entry, $pageId);
        }
        parent::__construct($entries);
    }

    /**
     * Creates a line of rST table-compliant string separators.
     *
     * @param string $char Character to use as a separator.
     * @return string
     */
    public static function socialNetworksUrlsSeparator(string $char): string
    {
        $outStr = "  ";
        $outStr .= "+" . str_repeat($char, 22);
        $outStr .= "+" . str_repeat($char, 52);
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
            $lines .= "\n  " . $entry->toTableLine() . "\n" . self::socialNetworksUrlsSeparator('-');
            $iterator->next();
        }
        $outStr = "\n" . self::socialNetworksUrlsSeparator('-') . "\n ";
        $outStr .= " | Name                ";
        $outStr .= " | URL                                               ";
        $outStr .= " |\n" . self::socialNetworksUrlsSeparator('=');
        $outStr .= $lines;
        return $outStr;
    }
}
