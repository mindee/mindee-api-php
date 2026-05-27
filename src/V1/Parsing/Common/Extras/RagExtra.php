<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common\Extras;

use Stringable;

/**
 * Contains information on the Retrieval-Augmented-Generation of a prediction.
 */
class RagExtra implements Stringable
{
    /**
     * @var string|null The document ID of the matching document.
     */
    public ?string $matchingDocumentId;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
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
