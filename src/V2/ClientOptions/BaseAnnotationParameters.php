<?php

declare(strict_types=1);

namespace Mindee\V2\ClientOptions;

/**
 * Base parameters for annotation operations.
 */
abstract class BaseAnnotationParameters
{
    /**
     * @param string $documentId Unique identifier of the document to annotate.
     */
    public function __construct(public readonly string $documentId) {}

    /**
     * @return array<string, mixed> Request parameters.
     */
    abstract public function getRequestParameters(): array;
}
