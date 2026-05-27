<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Ocr;

use Stringable;

use function strlen;

/**
 * Result of the Ocr utility inference.
 */
class OcrResult implements Stringable
{
    /**
     * @var OcrPage[] List of pages.
     */
    public array $pages;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->pages = array_map(static fn($page) => new OcrPage($page), $rawResponse['pages']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $str = "Ocr Result\n##########\n";
        $i = 1;

        foreach ($this->pages as $page) {
            $pageNumberTitle = "Page $i";
            $underline = str_repeat("=", strlen($pageNumberTitle));

            $str .= "$pageNumberTitle\n$underline\n\n$page\n";
            $i++;
        }

        return $str;
    }
}
