<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common\OCR;

/**
 * Mindee Vision V1.
 */
class MVisionV1
{
    /**
     * @var array<OCRPage> List of pages.
     */
    public array $pages;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $pagePrediction) {
            $this->pages[] = new OCRPage($pagePrediction);
        }
    }

    /**
     */
    public function __toString(): string
    {
        $pagesStr = [];
        foreach ($this->pages as $page) {
            $pagesStr[] = (string) $page;
        }
        return implode("\n", $pagesStr);
    }
}
