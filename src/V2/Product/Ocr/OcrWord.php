<?php

namespace Mindee\V2\Product\Ocr;

use Mindee\Geometry\Polygon;

/**
 * OCR result for a single word extracted from the document page.
 */
class OcrWord
{
    /**
     * @var string Content of the word.
     */
    public string $content;

    /**
     * @var Polygon Location which includes cropping coordinates for the detected object, within the source document.
     */
    public Polygon $polygon;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->content = $rawResponse['content'];
        $this->polygon = new Polygon($rawResponse['polygon']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->content;
    }
}
