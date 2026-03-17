<?php

namespace Mindee\V2\Product\Split;

/**
 * A single document as identified when splitting a multi-document source file.
 */
class SplitRange
{
    /**
     * @var integer[] 0-based page indexes, where the first integer indicates the start page and the second integer
     * indicates the end page.
     */
    public array $pageRange;

    /**
     * @var string Type or classification of the detected object.
     */
    public string $documentType;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->pageRange = $rawResponse['page_range'];
        $this->documentType = $rawResponse['document_type'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $pageRangeStr = implode(",", $this->pageRange);

        return "* :Page Range: $pageRangeStr\n  :Document Type: $this->documentType";
    }
}
