<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Search;

use DateTimeImmutable;
use Mindee\Parsing\DateHelper;

/**
 * Individual RAG document information.
 */
class RagDocument
{
    /**
     * @var string Unique identifier of the RAG document.
     */
    public string $id;
    /**
     * @var string Model identifier linked to the RAG document.
     */
    public string $modelId;
    /**
     * @var string Original filename of the uploaded document.
     */
    public string $filename;
    /**
     * @var DateTimeImmutable Date and time of the document creation.
     */
    public DateTimeImmutable $createdAt;
    /**
     * @var integer Number of times this document was used in an inference.
     */
    public int $totalMatches;
    /**
     * @var DateTimeImmutable|null Date and time of the latest matching inference, if any.
     */
    public ?DateTimeImmutable $lastMatchAt;
    /**
     * @var string Current status of the RAG document.
     */
    public string $status;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->id = $rawResponse['id'];
        $this->modelId = $rawResponse['model_id'];
        $this->filename = $rawResponse['filename'];
        $this->createdAt = new DateTimeImmutable($rawResponse['created_at']);
        $this->totalMatches = $rawResponse['total_matches'];
        $this->lastMatchAt = DateHelper::parseDateImmutable($rawResponse['last_match_at'] ?? null);
        $this->status = $rawResponse['status'];
    }
}
