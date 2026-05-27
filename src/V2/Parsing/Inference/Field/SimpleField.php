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
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     * @param integer $indentLevel Level of indentation for rst display.
     */
    public function __construct(array $rawResponse, int $indentLevel = 0)
    {
        parent::__construct($rawResponse, $indentLevel);
        $this->value = array_key_exists('value', $rawResponse) ? $rawResponse['value'] : null;
        if (is_int($this->value)) {
            $this->value = (float) $this->value;
        }
    }

    /**
     * @return string String representation.
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

    /**
     * @return string|null String representation of the field value.
     */
    public function getStringValue(): ?string
    {
        return null !== $this->value ? (string) $this->value : null;
    }

    /**
     * @return float|null Float representation of the field value.
     */
    public function getFloatValue(): ?float
    {
        return null !== $this->value ? (float) $this->value : null;
    }

    /**
     * @return integer|null Integer representation of the field value.
     */
    public function getIntValue(): ?int
    {
        return null !== $this->value ? (int) $this->value : null;
    }

    /**
     * @return boolean|null Boolean representation of the field value.
     */
    public function getBoolValue(): ?bool
    {
        return null !== $this->value ? (bool) $this->value : null;
    }
}
