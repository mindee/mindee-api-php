<?php

namespace Mindee\V2\Product\Ocr;

use Mindee\V2\Parsing\BaseInference;

/**
 * Response for an OCR utility inference.
 */
class OcrInference extends BaseInference
{
    /**
     * @var OcrResult Result of the inference.
     */
    public OcrResult $result;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->result = new OcrResult($rawResponse['result']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return parent::__toString() . "$this->result\n";
    }
}
