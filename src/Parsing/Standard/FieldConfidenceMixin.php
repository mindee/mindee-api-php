<?php

namespace Mindee\Parsing\Standard;

trait FieldConfidenceMixin
{
    public float $confidence;

    protected function setConfidence(array $raw_prediction)
    {
        if (array_key_exists('confidence', $raw_prediction) && $raw_prediction['confidence']) {
            $this->confidence = $raw_prediction['confidence'];
        } else {
            $this->confidence = 0.0;
        }
    }
}
