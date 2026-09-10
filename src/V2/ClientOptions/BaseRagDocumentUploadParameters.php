<?php

declare(strict_types=1);

namespace Mindee\V2\ClientOptions;

use InvalidArgumentException;
use Mindee\V2\Parsing\BaseRagAnnotationResponse;

/**
 *  Base parameters for annotation operations.
 * @template TAnnotationResponse of BaseRagAnnotationResponse
 */
abstract class BaseRagDocumentUploadParameters
{
    /**
     * @var class-string<TAnnotationResponse> $responseClass Response class.
     */
    protected static string $responseClass;

    /**
     * @param string $modelId UUID of the extraction model that the uploaded RAG document is linked to.
     */
    public function __construct(public readonly string $modelId) {}

    /**
     * Gets the response class associated with the parameters.
     *
     * @return class-string<TAnnotationResponse> Response class.
     */
    public function getResponseClass(): string
    {
        return static::$responseClass;
    }

    /**
     * Gets the request parameters for the upload request.
     * @return array<string, string> Request parameters.
     */
    public function getRequestParameters(): array
    {
        if (empty($this->modelId)) {
            throw new InvalidArgumentException("ModelId is required in RagDocumentsParameters");
        }

        return ['model_id' => $this->modelId];
    }
}
