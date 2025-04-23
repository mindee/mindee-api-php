<?php

namespace Mindee\Parsing\Common\Extras;

/**
 * Contains information on the Retrieval-Augmented-Generation of a prediction.
 */
class RAGExtra
{
    /**
     * @var string|null The document ID of the matching document.
     */
    public ?string $matchingDocumentId;

    /**
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->matchingDocumentId = $rawPrediction['matching_document_id'] ?? null;
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return isset($this->matchingDocumentId) ? "\n           " . $this->matchingDocumentId : '';
    }
}
