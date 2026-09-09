<?php

declare(strict_types=1);

namespace Mindee\V2\ClientOptions;

use Mindee\V2\Parsing\BaseRagAnnotationResponse;

/**
 * Base parameters for annotation operations.
 * @template TAnnotationResponse of BaseRagAnnotationResponse
 */
abstract class BaseAnnotationParameters
{
    /**
     * @var class-string<TAnnotationResponse> $responseClass Response class.
     */
    protected static string $responseClass;

    /**
     * @param string $documentId UUID of the annotated document.
     */
    public function __construct(public readonly string $documentId) {}

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
     * @return array<string, mixed> Request parameters.
     */
    abstract public function getRequestParameters(): array;
}
