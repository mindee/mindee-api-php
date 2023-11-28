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
     * @param array $raw_prediction Raw prediction array.
     */
    public function __construct(array $raw_prediction)
    {
        $this->pages = [];
        foreach ($raw_prediction['pages'] as $page_prediction) {
            $this->pages[] = new OcrPage($page_prediction);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $pages_str = [];
        foreach ($this->pages as $page) {
            $pages_str[] = strval($page);
        }
        return implode("\n", $pages_str);
    }
}
