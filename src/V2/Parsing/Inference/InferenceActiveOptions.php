<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference;

use Mindee\Parsing\SummaryHelper;
use Mindee\V2\Product\Extraction\Params\DataSchemaActiveOption;
use Stringable;

/**
 * Options which were activated during the inference.
 *
 * Options can be activated or deactivated:
 * - By setting their default values on the Platform UI
 * - By explicitly setting them in the inference request
 */
class InferenceActiveOptions implements Stringable
{
    /**
     * @var boolean Whether the Retrieval-Augmented Generation feature was activated.
     *              When this feature is activated, the RAG pipeline is used to increase result accuracy.
     */
    public bool $rag;

    /**
     * @var boolean Whether the Raw Text feature was activated.
     */
    public bool $rawText;

    /**
     * @var boolean Whether the Raw Text feature was activated.
     *              When this feature is activated, the raw text extracted from the document is returned in the result.
     */
    public bool $polygon;

    /**
     * @var boolean Whether the confidence feature was activated.
     *              When this feature is activated, a confidence score for each field is returned in the result.
     */
    public bool $confidence;
    /**
     * @var boolean Whether the text context feature was activated.
     *              When this feature is activated, the provided context is used to improve the accuracy of the inference.
     */
    public bool $textContext;

    /**
     * @var DataSchemaActiveOption Data schema options provided for the inference.
     */
    public DataSchemaActiveOption $dataSchema;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->rag = $rawResponse['rag'];
        $this->rawText = $rawResponse['raw_text'];
        $this->polygon = $rawResponse['polygon'];
        $this->confidence = $rawResponse['confidence'];
        $this->textContext = $rawResponse['text_context'];
        $this->dataSchema = new DataSchemaActiveOption($rawResponse['data_schema']);
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
            . $this->dataSchema;
    }
}
