<?php

namespace Mindee\V2\Product\Ocr;

/**
 * Result of the OCR utility inference.
 */
class OcrResult
{
    /**
     * @var OcrPage[] List of pages.
     */
    public array $pages;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->pages = array_map(fn ($page) => new OcrPage($page), $rawResponse['pages']);
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
