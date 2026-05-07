<?php

namespace Mindee\V2\Product\Classification;

use Mindee\Parsing\V2\InferenceResponse;

/**
 * Classification of document type from the source file.
 */
class ClassificationClassifier
{
    /**
     * @var string The document type, as identified on given classification values.
     */
    public string $documentType;

    /**
     * @var InferenceResponse|null $extractionResponse The extraction response associated with the classification.
     */
    public ?InferenceResponse $extractionResponse;

    /**
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->documentType = $rawPrediction['document_type'];
        $this->extractionResponse = isset($rawPrediction['extraction_response']) ?
            new InferenceResponse($rawPrediction['extraction_response']) : null;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Document Type: $this->documentType";
    }
}
