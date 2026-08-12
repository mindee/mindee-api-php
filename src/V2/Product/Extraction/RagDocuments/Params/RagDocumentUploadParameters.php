<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\RagDocuments\Params;

use InvalidArgumentException;

/**
 * Upload parameters for RAG documents.
 */
class RagDocumentUploadParameters
{
    /**
     * @param string $modelId UUID of the extraction model that the uploaded RAG document is linked to.
     */
    public function __construct(public readonly string $modelId) {}

    /**
     * @return array<string, string> Request parameters.
     * @throws InvalidArgumentException Throws if the model ID is missing.
     */
    public function getRequestParameters(): array
    {
        if (empty($this->modelId)) {
            throw new InvalidArgumentException("ModelId is required in RagDocumentsParameters");
        }

        return ['model_id' => $this->modelId];
    }
}
