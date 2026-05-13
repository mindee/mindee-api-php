<?php

declare(strict_types=1);

namespace Mindee\V2\Product\OCR;

use function strlen;

/**
 * Result of the OCR utility inference.
 */
class OCRResult
{
    /**
     * @var OCRPage[] List of pages.
     */
    public array $pages;

    /**
     * @param array<string, mixed> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->pages = array_map(static fn($page) => new OCRPage($page), $rawResponse['pages']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $str = "OCR Result\n##########\n";
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
