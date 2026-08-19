<?php

declare(strict_types=1);

namespace Mindee\V2\Search\RagDocuments;

use Mindee\V2\Parsing\Search\BaseSearchResponse;
use Mindee\V2\Parsing\Search\RagDocuments;

/**
 * RAG documents search response.
 */
class RagDocumentSearchResponse extends BaseSearchResponse
{
    /**
     * @var RagDocuments Paginated list of matching RAG documents.
     */
    public RagDocuments $ragDocuments;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->ragDocuments = new RagDocuments($rawResponse['rag_documents']);
    }

    /**
     * @return array<int, string> Body lines.
     */
    protected function bodyLines(): array
    {
        return [
            'RAG Documents',
            '############',
            (string) $this->ragDocuments,
        ];
    }
}
