<?php

namespace Mindee\Parsing\V2\Field;

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
}
