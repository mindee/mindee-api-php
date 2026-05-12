<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Classification;

use Mindee\V2\Product\Extraction\ExtractionResponse;

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
     * @var ExtractionResponse|null $extractionResponse The extraction response associated with the classification.
     */
    public ?ExtractionResponse $extractionResponse;

    /**
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->documentType = $rawPrediction['document_type'];
        $this->extractionResponse = isset($rawPrediction['extraction_response'])
            ? new ExtractionResponse($rawPrediction['extraction_response']) : null;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Document Type: $this->documentType";
    }
}
