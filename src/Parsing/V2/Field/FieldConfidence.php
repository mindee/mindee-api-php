<?php

namespace Mindee\Parsing\V2\Field;

use InvalidArgumentException;

/**
 * Confidence level of a field as returned by the V2 API.
 */
class FieldConfidence
{
    public const CERTAIN = 'Certain';
    public const HIGH = 'High';
    public const MEDIUM = 'Medium';
    public const LOW = 'Low';

    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $value Vale provided.
     * @throws InvalidArgumentException Throws if an invalid value is provided.
     */
    public function __construct(string $value)
    {
        if (!self::isValid($value)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid confidence value "%s". Valid values are: %s',
                $value,
                implode(', ', self::getValidValues())
            ));
        }
        $this->value = $value;
    }

    /**
     * @param string $value Value to check.
     * @return boolean True if the value is valid.
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::getValidValues(), true);
    }

    /**
     * @return array<string>
     */
    public static function getValidValues(): array
    {
        return [
            self::CERTAIN,
            self::HIGH,
            self::MEDIUM,
            self::LOW,
        ];
    }

    /**
     * @return string String representation.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
