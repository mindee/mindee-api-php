<?php

namespace Mindee\Product\Cropper;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;

/**
 * Document data for Cropper, API version 1.
 */
class CropperV1Document extends Prediction
{
    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "";
    }
}
