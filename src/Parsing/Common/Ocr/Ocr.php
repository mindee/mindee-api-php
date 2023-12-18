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
    public MVisionV1 $mvisionV1;

    /**
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->mvisionV1 = new MVisionV1($rawPrediction['mvision-v1']);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return strval($this->mvisionV1);
    }
}
