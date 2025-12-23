<?php

namespace Mindee\Parsing\V2;

use Mindee\Parsing\Common\SummaryHelper;

/**
 * Options which were activated during the inference.
 *
 * Options can be activated or deactivated:
 * - By setting their default values on the Platform UI
 * - By explicitly setting them in the inference request
 */
class InferenceActiveOptions
{
    /**
     * @var boolean Whether the Retrieval-Augmented Generation feature was activated.
     * When this feature is activated, the RAG pipeline is used to increase result accuracy.
     */
    public bool $rag;

    /**
     * @var boolean Whether the Raw Text feature was activated.
     */
    public bool $rawText;

    /**
     * @var boolean Whether the Raw Text feature was activated.
     * When this feature is activated, the raw text extracted from the document is returned in the result.
     */
    public bool $polygon;

    /**
     * @var boolean Whether the confidence feature was activated.
     * When this feature is activated, a confidence score for each field is returned in the result.
     */
    public bool $confidence;
    /**
     * @var boolean Whether the text context feature was activated.
     * When this feature is activated, the provided context is used to improve the accuracy of the inference.
     */
    public bool $textContext;

    /**
     * @var DataSchemaActiveOption Data schema options provided for the inference.
     */
    public DataSchemaActiveOption $dataSchema;

    /**
     * @param array $serverResponse Raw server response array.
     */
    public function __construct(array $serverResponse)
    {
        $this->rag = $serverResponse['rag'];
        $this->rawText = $serverResponse['raw_text'];
        $this->polygon = $serverResponse['polygon'];
        $this->confidence = $serverResponse['confidence'];
        $this->textContext = $serverResponse['text_context'];
        $this->dataSchema = new DataSchemaActiveOption($serverResponse['data_schema']);
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
            . ':RAG: ' . SummaryHelper::formatForDisplay($this->rag) . "\n"
            . ':Text Context: ' . SummaryHelper::formatForDisplay($this->textContext) . "\n\n"
            . $this->dataSchema . "\n";
    }
}
