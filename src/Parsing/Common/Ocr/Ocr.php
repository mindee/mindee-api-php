<?php

namespace Mindee\Parsing\Common\Ocr;

/**
 * OCR extraction from the entire document.
 */
class Ocr
{
    /**
     * @var \Mindee\Parsing\Common\Ocr\MVisionV1 Mindee Vision v1 results.
     */
    public MVisionV1 $mvision_v1;

    /**
     * @param array $raw_prediction Raw prediction array.
     */
    public function __construct(array $raw_prediction)
    {
        $this->mvision_v1 = new MVisionV1($raw_prediction['mvision-v1']);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return strval($this->mvision_v1);
    }
}
