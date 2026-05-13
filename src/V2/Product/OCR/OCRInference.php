<?php

declare(strict_types=1);

namespace Mindee\V2\Product\OCR;

use Mindee\V2\Parsing\BaseInference;

/**
 * Response for an OCR utility inference.
 */
class OCRInference extends BaseInference
{
    /**
     * @var OCRResult Result of the inference.
     */
    public OCRResult $result;

    /**
     * @param array<string, mixed> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        parent::__construct($rawResponse);
        $this->result = new OCRResult($rawResponse['result']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return parent::__toString() . "$this->result\n";
    }
}
