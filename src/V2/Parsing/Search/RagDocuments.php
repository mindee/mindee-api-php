<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Search;

use ArrayObject;
use Stringable;

/**
 * Array of RAG documents.
 * @extends ArrayObject<int, RagDocument>
 */
class RagDocuments extends ArrayObject implements Stringable
{
    /**
     * @param array<array<string, int|float|string|bool|null|array<array-key, mixed>>> $prediction Raw prediction.
     */
    public function __construct(array $prediction)
    {
        $documents = array_map(static fn($entry) => new RagDocument($entry), $prediction);

        parent::__construct($documents);
    }

    /**
     * Default string representation.
     */
    public function __toString(): string
    {
        if ($this->count() === 0) {
            return "\n";
        }

        $lines = [];
        foreach ($this as $document) {
            $lines[] = "* :ID: " . $document->id;
            $lines[] = "  :Model ID: " . $document->modelId;
            $lines[] = "  :Filename: " . $document->filename;
            $lines[] = "  :Created At: " . $document->createdAt->format(DATE_ATOM);
            $lines[] = "  :Total Matches: " . $document->totalMatches;
            $lines[] = "  :Last Match At: " . ($document->lastMatchAt?->format(DATE_ATOM) ?? '');
            $lines[] = "  :Status: " . $document->status;
        }

        return implode("\n", $lines) . "\n";
    }
}
