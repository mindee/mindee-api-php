<?php

namespace Mindee\Parsing\Common\Ocr;

class MVisionV1
{
    public array $pages;

    public function __construct(array $raw_prediction)
    {
        $this->pages = [];
        foreach ($raw_prediction['pages'] as $page_prediction) {
            $this->pages[] = new OcrPage($page_prediction);
        }
    }

    public function __toString(): string
    {
        $pages_str = [];
        foreach ($this->pages as $page) {
            $pages_str[] = strval($page);
        }
        return implode("\n", $pages_str);
    }
}
