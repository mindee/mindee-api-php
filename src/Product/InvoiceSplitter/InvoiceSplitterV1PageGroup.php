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
     * @param array $rawPrediction Array containing the JSON document response.
     */
    public function __construct(array $rawPrediction)
    {
        $this->pageIndexes = [];
        foreach ($rawPrediction['page_indexes'] as $pageIndex) {
            $this->pageIndexes[] = $pageIndex;
        }
        if (in_array('confidence', $rawPrediction) && is_numeric($rawPrediction['confidence'])) {
            $this->confidence = floatval($rawPrediction['confidence']);
        } else {
            $this->confidence = 0.0;
        }
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $outStr = ":Page indexes: ";
        $outStr .= implode(", ", $this->pageIndexes);
        return trim($outStr);
    }
}
