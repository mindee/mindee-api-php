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
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(
        array $rawPrediction
    ) {
        $this->value = $rawPrediction['value'];
        $this->confidence = $rawPrediction['confidence'];
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
