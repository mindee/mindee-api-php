<?php

namespace Mindee\parsing\custom;

class ClassificationField
{
    public string $value;
    public float $confidence;

    public function __construct(
        array $raw_prediction
    ) {
        $this->value = $raw_prediction['value'];
        $this->confidence = $raw_prediction['confidence'];
    }

    public function __toString(): string
    {
        return $this->value ?? '';
    }
}
