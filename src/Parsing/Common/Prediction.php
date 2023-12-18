<?php

namespace Mindee\Parsing\Common;

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
