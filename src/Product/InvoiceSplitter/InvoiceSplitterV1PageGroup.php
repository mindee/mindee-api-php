<?php

namespace Mindee\Product\InvoiceSplitter;

/**
 * Pages indexes in a group for Invoice Splitter V1.
 */
class InvoiceSplitterV1PageGroup
{
    /**
     * @var array Index of each page.
     */
    public array $pageIndexes;
    /**
     * @var float Confidence score.
     */
    public float $confidence;

    /**
     * @param array $raw_prediction Array containing the JSON document response.
     */
    public function __construct(array $raw_prediction)
    {
        $this->pageIndexes = [];
        foreach ($raw_prediction['page_indexes'] as $page_index) {
            $this->pageIndexes[] = $page_index;
        }
        if (in_array('confidence', $raw_prediction) && is_numeric($raw_prediction['confidence'])) {
            $this->confidence = floatval($raw_prediction['confidence']);
        } else {
            $this->confidence = 0.0;
        }
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $out_str = ":Page indexes: ";
        $out_str .= implode(", ", $this->pageIndexes);
        return trim($out_str);
    }
}
