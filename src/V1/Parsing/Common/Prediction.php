<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common;

/**
 * Base class for prediction responses.
 */
abstract class Prediction
{
    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return '';
    }
}
