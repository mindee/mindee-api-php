<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\RagDocuments;

use DateTimeImmutable;
use Mindee\Parsing\DateHelper;
use Mindee\V2\Parsing\BaseRagAnnotationResponse;

/**
 * Response for a RAG document annotation.
 */
class ExtractionRagAnnotationResponse extends BaseRagAnnotationResponse
{
    /**
     * @var string Model identifier linked to the RAG document.
     */
    public string $modelId;

    /**
     * @var int Number of times this document was used in an inference.
     */
    public int $totalMatches;

    /**
     * @var DateTimeImmutable|null Date and time of the latest matching inference, if any.
     */
    public ?DateTimeImmutable $lastMatchAt;

    /**
     * @var RagAnnotation|null Annotation metadata associated with the document.
     */
    public ?RagAnnotation $annotation;

    /**
     * @param array<string, mixed> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->modelId = $rawResponse['model_id'];
        $this->totalMatches = $rawResponse['total_matches'];
        $this->lastMatchAt = DateHelper::parseDateImmutable($rawResponse['last_match_at'] ?? null);
        $this->annotation = isset($rawResponse['annotation'])
            ? new RagAnnotation($rawResponse['annotation'])
            : null;
    }
}
