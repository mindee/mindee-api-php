<?php

namespace Mindee\Parsing\Custom;

/**
 * A classification field.
 */
class ClassificationField
{
    /**
     * @var string|mixed|null The classification value.
     */
    public ?string $value;
    /**
     * @var float|mixed The confidence score.
     */
    public float $confidence;

    /**
     * @param array $raw_prediction Raw prediction array.
     */
    public function __construct(
        array $raw_prediction
    ) {
        $this->value = $raw_prediction['value'];
        $this->confidence = $raw_prediction['confidence'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
