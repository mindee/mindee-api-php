<?php

namespace Mindee\V2\Product\Classification;

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
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->documentType = $rawPrediction['document_type'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Document Type: $this->documentType";
    }
}
