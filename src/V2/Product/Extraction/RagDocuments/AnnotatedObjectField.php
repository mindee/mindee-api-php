<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Extraction\RagDocuments;

use InvalidArgumentException;

/**
 * An ObjectField with additional configuration for annotation.
 */
class AnnotatedObjectField extends AnnotatedBaseField
{
    /**
     */
    public function __construct(bool $selected, ?string $guidelines, /**
     * @var AnnotatedFields Sub-fields of the field.
     */
        public AnnotatedFields $fields)
    {
        parent::__construct($selected, $guidelines);
    }

    /**
     * @param array<string, mixed> $rawResponse Raw server response array.
     */
    public static function fromArray(array $rawResponse): self
    {
        $selected = (bool) ($rawResponse['selected'] ?? false);
        $guidelines = isset($rawResponse['guidelines']) ? (string) $rawResponse['guidelines'] : null;
        $fields = new AnnotatedFields($rawResponse['fields'] ?? []);

        return new self($selected, $guidelines, $fields);
    }

    /**
     * Returns an AnnotatedSimpleField instance for the specified key.
     *
     * @param string $key The key of the simple field to retrieve.
     * @throws InvalidArgumentException When the field does not exist or is not a simple field.
     */
    public function getSimpleField(string $key): AnnotatedSimpleField
    {
        return $this->fields->getSimpleField($key);
    }

    /**
     * Returns an AnnotatedListField instance for the specified key.
     *
     * @param string $key The key of the list field to retrieve.
     * @throws InvalidArgumentException When the field does not exist or is not a list field.
     */
    public function getListField(string $key): AnnotatedListField
    {
        return $this->fields->getListField($key);
    }

    /**
     * Returns an AnnotatedObjectField instance for the specified key.
     *
     * @param string $key The key of the object field to retrieve.
     * @throws InvalidArgumentException When the field does not exist or is not an object field.
     */
    public function getObjectField(string $key): self
    {
        return $this->fields->getObjectField($key);
    }

    /**
     * Returns an array of all AnnotatedSimpleField instances in this object's fields.
     *
     * @return AnnotatedSimpleField[]
     */
    public function getSimpleFields(): array
    {
        $out = [];
        foreach ($this->fields->getArrayCopy() as $field) {
            if ($field instanceof AnnotatedSimpleField) {
                $out[] = $field;
            }
        }

        return $out;
    }

    /**
     * Returns an array of all AnnotatedListField instances in this object's fields.
     *
     * @return AnnotatedListField[]
     */
    public function getListFields(): array
    {
        $out = [];
        foreach ($this->fields->getArrayCopy() as $field) {
            if ($field instanceof AnnotatedListField) {
                $out[] = $field;
            }
        }

        return $out;
    }

    /**
     * Returns an array of all AnnotatedObjectField instances in this object's fields.
     *
     * @return AnnotatedObjectField[]
     */
    public function getObjectFields(): array
    {
        $out = [];
        foreach ($this->fields->getArrayCopy() as $field) {
            if ($field instanceof self) {
                $out[] = $field;
            }
        }

        return $out;
    }

    /**
     * @return array<string, mixed> Array representation for serialization.
     */
    public function toArray(): array
    {
        return [
            'selected' => $this->selected,
            'guidelines' => $this->guidelines,
            'fields' => $this->fields->toArray(),
        ];
    }
}
