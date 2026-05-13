<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference;

/**
 * Metadata about the RAG operation.
 */
class RAGMetadata
{
    /**
     * @var string|null ID of the matched document, if present.
     */
    public ?string $retrievedDocumentId;

    /**
     * @param array<string, mixed> $rawResponse Raw response from the server.
     */
    public function __construct(array $rawResponse)
    {
        $this->retrievedDocumentId = $rawResponse['retrieved_document_id'] ?? null;
    }
}
