<?php

namespace Mindee\Product\InvoiceSplitter;

class InvoiceSplitterV1PageGroup
{
    public array $pageIndexes;
    public float $confidence;

    function __construct(array $raw_prediction)
    {
        $this->pageIndexes = [];
        foreach ($raw_prediction['page_indexes'] as $page_index) {
            $this->pageIndexes[] = $page_index;
        }
        if (in_array('confidence', $raw_prediction) && is_numeric($raw_prediction['confidence'])) {
            $this->confidence = $raw_prediction['confidence'];
        } else {
            $this->confidence = 0.0;
        }
    }

    public function __toString(): string
    {
        $out_str = ":Page indexes: ";
        $out_str .= implode(", ", $this->pageIndexes);
        return trim($out_str);
    }
}
