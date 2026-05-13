<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference\Field;

use ArrayObject;
use InvalidArgumentException;

use function sprintf;

/**
 * Collection of inference fields.
 * @extends ArrayObject<string, SimpleField|ObjectField|ListField>
 */
class InferenceFields extends ArrayObject
{
    /**
     * @var array<string, SimpleField|ObjectField|ListField>
     */
    private array $fields = [];

    /**
     * @var integer Indentation level.
     */
    private int $indentLevel;

    /**
     * @param array<string,mixed> $rawResponse Raw server response array.
     * @param integer $indentLevel Level of indentation.
     */
    public function __construct(array $rawResponse, int $indentLevel = 0)
    {
        $this->indentLevel = $indentLevel;

        foreach ($rawResponse as $key => $value) {
            $this->fields[$key] = BaseField::createField($value, 1);
        }
        parent::__construct($this->fields);
    }

    /**
     * Get a field by key.
     *
     * @param string $fieldName Field key to retrieve.
     * @return SimpleField|ObjectField|ListField
     * @throws InvalidArgumentException When the field does not exist.
     */
    public function get(string $fieldName): SimpleField|ObjectField|ListField
    {
        return $this->fields[$fieldName] ?? throw new InvalidArgumentException("Field $fieldName does not exist.");
    }

    /**
     * Get a simple field by key.
     *
     * @param string $fieldName Field key to retrieve.
     * @return SimpleField
     * @throws InvalidArgumentException When the field does not exist or is not a simple field.
     */
    public function getSimpleField(string $fieldName)
    {
        $field = $this->get($fieldName);
        if ($field instanceof SimpleField) {
            return $field;
        }
        throw new InvalidArgumentException("Field $fieldName is not a simple field.");
    }

    /**
     * Get a list field by key.
     *
     * @param string $fieldName Field key to retrieve.
     * @return ListField
     * @throws InvalidArgumentException When the field does not exist or is not a list field.
     */
    public function getListField(string $fieldName)
    {
        $field = $this->get($fieldName);
        if ($field instanceof ListField) {
            return $field;
        }
        throw new InvalidArgumentException("Field $fieldName is not a list field.");
    }

    /**
     * Get a simple field by key.
     *
     * @param string $fieldName Field key to retrieve.
     * @return ObjectField
     * @throws InvalidArgumentException When the field does not exist or is not an object field.
     */
    public function getObjectField(string $fieldName)
    {
        $field = $this->get($fieldName);
        if ($field instanceof ObjectField) {
            return $field;
        }
        throw new InvalidArgumentException("Field $fieldName is not an object field.");
    }

    /**
     * Convert the fields to a string representation.
     *
     * @param integer|null $indent Optional indentation level.
     */
    public function toString(?int $indent = 0): string
    {
        if ($this->count() === 0) {
            return '';
        }

        $indent ??= $this->indentLevel;
        $padding = str_repeat('  ', $indent);
        $lines = [];

        foreach ($this->fields as $fieldKey => $fieldValue) {
            $line = sprintf('%s:%s:', $padding, $fieldKey);

            if ($fieldValue instanceof ListField) {
                if (!empty($fieldValue->items)) {
                    $line .= $fieldValue->__toString();
                }
            } elseif ($fieldValue instanceof ObjectField) {
                $line .= $fieldValue->__toString();
            } elseif ($fieldValue instanceof SimpleField) {
                $value = $fieldValue->__toString();
                if ($value !== '') {
                    $line .= ' ' . $value;
                }
            }

            $lines[] = $line;
        }

        return rtrim(implode("\n", $lines));
    }
}
