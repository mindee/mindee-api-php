<?php

namespace Mindee\Parsing\V2\Field;

/**
 * A simple field with a scalar value.
 */
class SimpleField extends BaseField
{
    /**
     * @var string | integer | float | boolean | null Value contained in the field.
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
        if (is_bool($this->value)) {
            return $this->value ? 'True' : 'False';
        }
        if (is_numeric($this->value)) {
            $value = (float)$this->value;
            return $value == (int)$value ?
                number_format($value, 1, '.', '') :
                rtrim(rtrim(number_format($value, 5, '.', ''), '0'), '.');
        }

        return $this->value !== null ? (string)$this->value : '';
    }
}
