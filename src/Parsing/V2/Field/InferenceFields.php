<?php

namespace Mindee\Parsing\V2\Field;

use ArrayObject;

/**
 * Collection of inference fields.
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
     * @param array   $serverResponse Raw server response array.
     * @param integer $indentLevel    Level of indentation.
     */
    public function __construct(array $serverResponse, int $indentLevel = 0)
    {
        $this->indentLevel = $indentLevel;

        foreach ($serverResponse as $key => $value) {
            $this->fields[$key] = BaseField::createField($value, 1);
        }
        parent::__construct($this->fields);
    }

    /**
     * Get a field by key.
     *
     * @param string $key Field key to retrieve.
     * @return SimpleField|ObjectField|ListField|null
     */
    public function get(string $key)
    {
        return $this->fields[$key] ?? null;
    }

    /**
     * Convert the fields to a string representation.
     *
     * @param integer|null $indent Optional indentation level.
     * @return string
     */
    public function toString(?int $indent = 0): string
    {
        if ($this->count() === 0) {
            return '';
        }

        $indent = $indent ?? $this->indentLevel;
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
                if ($value != '') {
                    $line .= ' ' . $value;
                }
            }

            $lines[] = $line;
        }

        return rtrim(implode("\n", $lines));
    }
}
