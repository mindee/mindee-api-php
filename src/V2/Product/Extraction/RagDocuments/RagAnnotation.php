<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\RagDocuments;

/**
 * A RAG annotation enriched with field-level configuration.
 */
class RagAnnotation
{
    /**
     * @var AnnotatedFields Annotated fields.
     */
    public AnnotatedFields $fields;

    /**
     * @param array<string, mixed> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->fields = new AnnotatedFields($rawResponse['fields'] ?? []);
    }

    /**
     * @return array<string, mixed> Array representation for serialization.
     */
    public function toArray(): array
    {
        return ['fields' => $this->fields->toArray()];
    }
}
