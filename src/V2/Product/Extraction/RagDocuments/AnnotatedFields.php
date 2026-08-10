<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\RagDocuments;

use ArrayObject;
use InvalidArgumentException;

/**
 * A dictionary of field names and their corresponding annotation.
 *
 * @extends ArrayObject<string, AnnotatedSimpleField|AnnotatedObjectField|AnnotatedListField>
 */
class AnnotatedFields extends ArrayObject
{
    /**
     * @var array<string, AnnotatedSimpleField|AnnotatedObjectField|AnnotatedListField>
     */
    private array $fields = [];

    /**
     * @param array<string, array<string, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        foreach ($rawResponse as $key => $value) {
            $this->fields[$key] = AnnotatedBaseField::createField($value);
        }

        parent::__construct($this->fields);
    }

    /**
     * Get a field by key.
     *
     * @param string $fieldName Field key to retrieve.
     * @throws InvalidArgumentException When the field does not exist.
     */
    public function get(string $fieldName): AnnotatedSimpleField|AnnotatedObjectField|AnnotatedListField
    {
        return $this->fields[$fieldName] ?? throw new InvalidArgumentException("Field $fieldName does not exist.");
    }

    /**
     * Get a simple field by key.
     *
     * @param string $fieldName Field key to retrieve.
     * @throws InvalidArgumentException When the field does not exist or is not a simple field.
     */
    public function getSimpleField(string $fieldName): AnnotatedSimpleField
    {
        $field = $this->get($fieldName);
        if ($field instanceof AnnotatedSimpleField) {
            return $field;
        }
        throw new InvalidArgumentException("Field $fieldName is not a simple field.");
    }

    /**
     * Get a list field by key.
     *
     * @param string $fieldName Field key to retrieve.
     * @throws InvalidArgumentException When the field does not exist or is not a list field.
     */
    public function getListField(string $fieldName): AnnotatedListField
    {
        $field = $this->get($fieldName);
        if ($field instanceof AnnotatedListField) {
            return $field;
        }
        throw new InvalidArgumentException("Field $fieldName is not a list field.");
    }

    /**
     * Get an object field by key.
     *
     * @param string $fieldName Field key to retrieve.
     * @throws InvalidArgumentException When the field does not exist or is not an object field.
     */
    public function getObjectField(string $fieldName): AnnotatedObjectField
    {
        $field = $this->get($fieldName);
        if ($field instanceof AnnotatedObjectField) {
            return $field;
        }
        throw new InvalidArgumentException("Field $fieldName is not an object field.");
    }

    /**
     * @return array<string, mixed> Array representation for serialization.
     */
    public function toArray(): array
    {
        $out = [];
        foreach ($this->fields as $key => $field) {
            $out[$key] = $field->toArray();
        }

        return $out;
    }
}
