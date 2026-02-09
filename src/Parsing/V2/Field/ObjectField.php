<?php

namespace Mindee\Parsing\V2\Field;

use InvalidArgumentException;

/**
 * A field containing a nested set of inference fields.
 */
class ObjectField extends BaseField
{
    /**
     * @var InferenceFields
     */
    public InferenceFields $fields;

    /**
     * @param array   $serverResponse Raw server response array.
     * @param integer $indentLevel    Level of indentation for rst display.
     */
    public function __construct(array $serverResponse, int $indentLevel = 0)
    {
        parent::__construct($serverResponse, $indentLevel);

        $this->fields = new InferenceFields(
            $serverResponse['fields'],
            $this->indentLevel + 1
        );
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "\n" . ($this->fields->toString(1));
    }

    /**
     * Returns a string representation suitable for list display.
     *
     * @return string
     */
    public function toStringFromList(): string
    {
        return substr($this->fields->toString(2), 4);
    }

    /**
     * Returns a ListField instance for the specified key.
     *
     * @param string $key The key of the list field to retrieve.
     * @return ListField
     * @throws InvalidArgumentException When the field does not exist or is not a list field.
     */
    public function getListField(string $key): ListField
    {
        $field = $this->fields->get($key);
        if (!($field instanceof ListField)) {
            throw new InvalidArgumentException("Field $key is not a list field.");
        }
        return $field;
    }

    /**
     * Returns a SimpleField instance for the specified key.
     *
     * @param string $key The key of the simple field to retrieve.
     * @return SimpleField
     * @throws InvalidArgumentException When the field does not exist or is not a simple field.
     */
    public function getSimpleField(string $key): SimpleField
    {
        $field = $this->fields->get($key);
        if (!($field instanceof SimpleField)) {
            throw new InvalidArgumentException("Field $key is not a simple field.");
        }
        return $field;
    }

    /**
     * Returns an ObjectField instance for the specified key.
     *
     * @param string $key The key of the simple field to retrieve.
     * @return ObjectField
     * @throws InvalidArgumentException When the field does not exist or is not a simple field.
     */
    public function getObjectField(string $key): ObjectField
    {
        $field = $this->fields->get($key);
        if (!($field instanceof ObjectField)) {
            throw new InvalidArgumentException("Field $key is not a simple field.");
        }
        return $field;
    }

    /**
     * Returns an array of SimpleField instances.
     *
     * @return SimpleField[]
     * @throws InvalidArgumentException When a field does not exist or is not a simple field.
     */
    public function getSimpleFields(): array
    {
        $out = [];
        foreach ($this->fields->getArrayCopy() as $field) {
            if ($field instanceof SimpleField) {
                $out[] = $field;
            }
        }
        return $out;
    }

    /**
     * Returns an array of ListField instances.
     *
     * @return ListField[]
     * @throws InvalidArgumentException When a field does not exist or is not a list field.
     */
    public function getListFields(): array
    {
        $out = [];
        foreach ($this->fields->getArrayCopy() as $field) {
            if ($field instanceof ListField) {
                $out[] = $field;
            }
        }
        return $out;
    }

    /**
     * Returns an array of ObjectField instances.
     *
     * @return ObjectField[]
     * @throws InvalidArgumentException When a field does not exist or is not an object field.
     */
    public function getObjectFields(): array
    {
        $out = [];
        foreach ($this->fields->getArrayCopy() as $field) {
            if ($field instanceof ObjectField) {
                $out[] = $field;
            }
        }
        return $out;
    }
}
