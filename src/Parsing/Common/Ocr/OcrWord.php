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
     * @param array $raw_prediction Raw prediction array.
     */
    public function __construct(array $raw_prediction)
    {
        $this->confidence = $raw_prediction['confidence'];
        $this->text       = $raw_prediction['text'];
        $this->setPosition($raw_prediction);
    }


    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return $this->text;
    }
}
