<?php

namespace Mindee\Parsing\Common\Ocr;

use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * A single word.
 */
class OcrWord
{
    use FieldPositionMixin;

    /**
     * @var float|mixed The confidence score.
     */
    public float $confidence;

    /**
     * @var string|mixed The extracted text.
     */
    public string $text;


    /**
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(array $rawPrediction)
    {
        $this->confidence = $rawPrediction['confidence'];
        $this->text       = $rawPrediction['text'];
        $this->setPosition($rawPrediction);
    }


    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->text;
    }
}
