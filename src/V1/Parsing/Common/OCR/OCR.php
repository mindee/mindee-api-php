<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common\OCR;

use Stringable;

use function array_key_exists;
use function count;

/**
 * OCR extraction from the entire document.
 */
class OCR implements Stringable
{
    /**
     * @var MVisionV1 Mindee Vision v1 results.
     */
    public MVisionV1 $mvisionV1;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->mvisionV1 = new MVisionV1($rawPrediction['mvision-v1']);
    }

    /**
     */
    public function __toString(): string
    {
        return (string) ($this->mvisionV1);
    }

    /**
     * Finds all lines matching the given regex in the OCR data, indexed by their page.
     *
     * @param string $regex The regular expression to match against.
     * @return array<integer, array<OCRLine>> All lines that match the regex, indexed by their page.
     */
    public function findLineByRegex(string $regex): array
    {
        $matches = [];
        for ($i = 0; $i < count($this->mvisionV1->pages); $i++) {
            $page = $this->mvisionV1->pages[$i];
            foreach ($page->getAllLines() as $line) {
                if (preg_match($regex, (string) $line)) {
                    if (!array_key_exists($i, $matches)) {
                        $matches[$i] = [];
                    }
                    $matches[$i][] = $line;
                }
            }
        }
        return $matches;
    }
}
