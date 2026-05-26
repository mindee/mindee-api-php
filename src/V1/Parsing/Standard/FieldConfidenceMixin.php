<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Standard;

use function array_key_exists;

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
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     */
    protected function setConfidence(array $rawPrediction): void
    {
        if (array_key_exists('confidence', $rawPrediction) && $rawPrediction['confidence']) {
            $this->confidence = $rawPrediction['confidence'];
        } else {
            $this->confidence = 0.0;
        }
    }
}
