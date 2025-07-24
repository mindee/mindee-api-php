<?php

namespace Mindee\Parsing\V2\Field;

/**
 * A simple field with a scalar value.
 */
class SimpleField extends BaseField
{
    /**
     * @var string | int | float | boolean | null Value contained in the field.
     */
    public $value;

    /**
     * @param array   $serverResponse Raw server response array.
     * @param integer $indentLevel    Level of indentation for rst display.
     */
    public function __construct(array $serverResponse, int $indentLevel = 0)
    {
        parent::__construct($serverResponse, $indentLevel);
        $this->value = array_key_exists('value', $serverResponse) ? $serverResponse['value'] : null;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value !== null ? (string) $this->value : '';
    }
}
