<?php

namespace Mindee\V2\Product\Split;

use Mindee\Parsing\V2\InferenceResponse;

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
     * @var InferenceResponse|null $extractionResponse The extraction response associated with the split.
     */
    public ?InferenceResponse $extractionResponse;


    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->pageRange = $rawResponse['page_range'];
        $this->documentType = $rawResponse['document_type'];
        $this->extractionResponse = isset($rawResponse['extraction_response']) ?
            new InferenceResponse($rawResponse['extraction_response']) : null;
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
