<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Resume;

use ArrayObject;

/**
 * The list of social network profiles of the candidate.
 * @extends ArrayObject<integer, ResumeV1SocialNetworksUrl>
 */
class ResumeV1SocialNetworksUrls extends ArrayObject
{
    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @param integer|null $pageId Page number for multi pages document.
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
