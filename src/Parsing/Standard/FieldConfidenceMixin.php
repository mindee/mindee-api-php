<?php

namespace Mindee\Parsing\Standard;

/**
 * Trait to add position information.
 */
trait FieldConfidenceMixin
{
    /**
     * @var float The confidence score.
     */
    public float $confidence;

    /**
     * Sets the confidence score.
     *
     * @param array $raw_prediction Raw prediction array.
     * @return void
     */
    protected function setConfidence(array $raw_prediction)
    {
        if (array_key_exists('confidence', $raw_prediction) && $raw_prediction['confidence']) {
            $this->confidence = $raw_prediction['confidence'];
        } else {
            $this->confidence = 0.0;
        }
    }
}
