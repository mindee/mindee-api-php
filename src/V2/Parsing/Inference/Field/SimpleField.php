<?php

declare(strict_types=1);

namespace Mindee\V2\Parsing\Inference\Field;

use function array_key_exists;
use function is_bool;
use function is_int;

/**
 * A simple field with a scalar value.
 */
class SimpleField extends BaseField
{
    /**
     * @var string|float|boolean|null Value contained in the field.
     */
    public $value;

    /**
     * @param array $serverResponse Raw server response array.
     * @param integer $indentLevel Level of indentation for rst display.
     */
    public function __construct(array $serverResponse, int $indentLevel = 0)
    {
        parent::__construct($serverResponse, $indentLevel);
        $this->value = array_key_exists('value', $serverResponse) ? $serverResponse['value'] : null;
        if (is_int($this->value)) {
            $this->value = (float) $this->value;
        }
    }

    /**
     */
    public function __toString(): string
    {
        if (is_bool($this->value)) {
            return $this->value ? 'True' : 'False';
        }
        if (is_numeric($this->value)) {
            return number_format((float) $this->value, 1, '.', '');
        }
        return $this->value !== null ? (string) $this->value : '';
    }
}
