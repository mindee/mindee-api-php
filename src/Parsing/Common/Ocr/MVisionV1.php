<?php

namespace Mindee\Parsing\Common\Ocr;

/**
 * Mindee Vision V1.
 */
class MVisionV1
{
    /**
     * @var array List of pages.
     */
    public array $pages;

    /**
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->pages = [];
        foreach ($rawPrediction['pages'] as $pagePrediction) {
            $this->pages[] = new OcrPage($pagePrediction);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $pagesStr = [];
        foreach ($this->pages as $page) {
            $pagesStr[] = strval($page);
        }
        return implode("\n", $pagesStr);
    }
}
