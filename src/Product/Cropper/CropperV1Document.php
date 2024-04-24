<?php

namespace Mindee\Product\Cropper;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;

/**
 * Cropper API version 1.1 document data.
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
