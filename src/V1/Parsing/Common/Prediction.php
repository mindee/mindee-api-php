<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common;

use Stringable;

/**
 * Base class for prediction responses.
 */
abstract class Prediction implements Stringable
{
    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return '';
    }
}
