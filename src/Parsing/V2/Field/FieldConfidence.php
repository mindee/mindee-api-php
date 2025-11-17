<?php

namespace Mindee\Parsing\V2\Field;

enum FieldConfidence: string
{
    case Certain = 'Certain';
    case High = 'High';
    case Medium = 'Medium';
    case Low = 'Low';

    /**
     * @return integer Rank of the value.
     */
    public function rank(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Medium => 2,
            self::High => 3,
            self::Certain => 4,
        };
    }

    /**
     * Shorthand for the '<=' operator.
     * @param FieldConfidence $other Other confidence value.
     * @return boolean True if this confidence is lower than or equal to the other.
     */
    public function lessThanOrEqual(FieldConfidence $other): bool
    {
        return $this->rank() <= $other->rank();
    }

    /**
     * Shorthand for the '>=' operator.
     * @param FieldConfidence $other Other confidence value.
     * @return boolean True if this confidence is greater than or equal to the other.
     */
    public function greaterThanOrEqual(FieldConfidence $other): bool
    {
        return $this->rank() >= $other->rank();
    }

    /**
     * Shorthand for the '<' operator.
     * @param FieldConfidence $other Other confidence value.
     * @return boolean True if this confidence is lower than the other.
     */
    public function lessThan(FieldConfidence $other): bool
    {
        return $this->rank() < $other->rank();
    }


    /**
     * Shorthand for the '>' operator.
     * @param FieldConfidence $other Other confidence value.
     * @return boolean True if this confidence is greater than the other.
     */
    public function greaterThan(FieldConfidence $other): bool
    {
        return $this->rank() > $other->rank();
    }


    /**
     * Shorthand for the '==' operator.
     * @param FieldConfidence $other Other confidence value.
     * @return boolean True if this confidence is equal to the other.
     */
    public function equal(FieldConfidence $other): bool
    {
        return $this === $other;
    }
}
