<?php

namespace Mindee\Parsing\Common\Ocr;

use Mindee\Parsing\Standard\FieldPositionMixin;

class OcrWord
{
    use FieldPositionMixin;

    public float $confidence;

    public string $text;


    public function __construct(array $raw_prediction)
    {
        $this->confidence = $raw_prediction['confidence'];
        $this->text       = $raw_prediction['text'];
        $this->setPosition($raw_prediction);
    }


    public function __toString(): string
    {
        return $this->text;
    }
}
