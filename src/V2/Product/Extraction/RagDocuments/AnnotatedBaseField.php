<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\RagDocuments;

use Mindee\Error\MindeeApiException;

use function array_key_exists;
use function sprintf;

/**
 * Base class for annotated fields.
 */
abstract class AnnotatedBaseField
{
    /**
     */
    public function __construct(
        /**
         * @var bool When true, use the RAG information for the final result. When false, use the Data Schema information.
         */
        public bool $selected,
        /**
         * @var string|null Guidelines or instructions for processing this field.
         */
        public ?string $guidelines
    ) {}

    /**
     * @param array<string, mixed> $rawResponse Raw server response array.
     * @throws MindeeApiException Throws if the field type is not recognized.
     */
    public static function createField(array $rawResponse): AnnotatedSimpleField|AnnotatedObjectField|AnnotatedListField
    {
        if (array_key_exists('items', $rawResponse)) {
            return AnnotatedListField::fromArray($rawResponse);
        }
        if (array_key_exists('fields', $rawResponse)) {
            return AnnotatedObjectField::fromArray($rawResponse);
        }
        if (array_key_exists('value', $rawResponse)) {
            return AnnotatedSimpleField::fromArray($rawResponse);
        }
        throw new MindeeApiException(
            sprintf('Unrecognized annotated field format in %s.', json_encode($rawResponse))
        );
    }

    /**
     * @return array<string, mixed> Array representation for serialization.
     */
    abstract public function toArray(): array;
}
