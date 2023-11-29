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
     * @param array $rawPrediction Raw prediction array.
     * @return void
     */
    protected function setConfidence(array $rawPrediction)
    {
        if (array_key_exists('confidence', $rawPrediction) && $rawPrediction['confidence']) {
            $this->confidence = $rawPrediction['confidence'];
        } else {
            $this->confidence = 0.0;
        }
    }
}
