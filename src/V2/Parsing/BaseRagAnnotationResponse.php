<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing;

use DateTimeImmutable;
use Mindee\V2\Parsing\Inference\BaseResponse;

/**
 * Base class for all RAG document responses from the V2 API.
 */
class BaseRagAnnotationResponse extends BaseResponse
{
    /**
     * @var string Unique identifier of the RAG document.
     */
    public string $id;

    /**
     * @var string Original filename of the uploaded document.
     */
    public string $filename;

    /**
     * @var DateTimeImmutable Date and time of the document creation.
     */
    public DateTimeImmutable $createdAt;

    /**
     * @var string Current status of the RAG document.
     */
    public string $status;

    /**
     * @param array<string, mixed> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->id = $rawResponse['id'];
        $this->filename = $rawResponse['filename'];
        $this->createdAt = new DateTimeImmutable($rawResponse['created_at']);
        $this->status = $rawResponse['status'];
    }
}
