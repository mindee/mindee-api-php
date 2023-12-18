<?php

namespace Mindee\Product\Us\W9;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;

/**
 * Document data for W9, API version 1.
 */
class W9V1Document extends Prediction
{
    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "";
    }
}
