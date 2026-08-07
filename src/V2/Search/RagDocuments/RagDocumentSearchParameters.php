<?php

declare(strict_types=1);

namespace Mindee\V2\Search\RagDocuments;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeException;
use Mindee\V2\ClientOptions\BaseSearchParameters;

/**
 * Search parameters for RAG documents.
 */
class RagDocumentSearchParameters extends BaseSearchParameters
{
    /**
     * @param string|null $modelId Model identifier to search in (required).
     * @param string|null $filename Case-insensitive substring search on filename.
     * @param integer|null $page 1-based page index.
     * @param integer|null $perPage Number of items per page.
     */
    public function __construct(
        public ?string $modelId = null,
        public ?string $filename = null,
        ?int $page = null,
        ?int $perPage = null
    ) {
        parent::__construct($page, $perPage);
    }

    /**
     * @return array<string, string> Query parameters.
     * @throws MindeeException Throws if the model ID is not provided.
     */
    public function getQueryParams(): array
    {
        $params = parent::getQueryParams();
        if (!empty($this->modelId)) {
            $params['model_id'] = $this->modelId;
        } else {
            throw new MindeeException(
                "ModelId is required in RagDocumentSearchParameters.",
                ErrorCode::USER_INPUT_ERROR
            );
        }
        if (!empty($this->filename)) {
            $params['filename'] = $this->filename;
        }
        return $params;
    }
}
