<?php

namespace Mindee\Parsing\V2;

/**
 * Metadata about the RAG operation.
 */
class RagMetadata
{
    /**
     * @var string|null ID of the matched document, if present.
     */
    public ?string $retrievedDocumentId;

    /**
     * @param array $rawResponse Raw response from the server.
     */
    public function __construct(array $rawResponse)
    {
        $this->retrievedDocumentId = $rawResponse['retrieved_document_id'] ?? null;
    }
}
