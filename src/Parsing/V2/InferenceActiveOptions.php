<?php

namespace Mindee\Parsing\V2;

use Mindee\Parsing\Common\SummaryHelper;

/**
 * Inference result options class.
 */
class InferenceActiveOptions
{
    /**
     * @var boolean Whether the RAG feature was activated.
     */
    public bool $rag;

    /**
     * @var boolean Whether the Raw Text feature was activated.
     */
    public bool $rawText;

    /**
     * @var boolean Whether the polygon feature was activated.
     */
    public bool $polygon;

    /**
     * @var boolean Whether the confidence feature was activated.
     */
    public bool $confidence;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->rag = $serverResponse['rag'];
        $this->rawText = $serverResponse['raw_text'];
        $this->polygon = $serverResponse['polygon'];
        $this->confidence = $serverResponse['confidence'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Active Options\n==============\n"
            . ':Raw Text: ' . SummaryHelper::formatForDisplay($this->rawText) . "\n"
            . ':Polygon: ' . SummaryHelper::formatForDisplay($this->polygon) . "\n"
            . ':Confidence: ' . SummaryHelper::formatForDisplay($this->confidence) . "\n"
            . ':RAG: ' . SummaryHelper::formatForDisplay($this->rag) . "\n";
    }
}
