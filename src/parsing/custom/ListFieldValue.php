<?php


namespace Mindee\parsing\custom;

use Mindee\parsing\standard\FieldPositionMixin;

class ListFieldValue
{
    use FieldPositionMixin;

    public string $content;
    public float $confidence;

    public function __construct(
        array $raw_prediction
    )
    {
        $this->content = $raw_prediction['content'];
        $this->confidence = $raw_prediction['confidence'];
        $this->setPosition($raw_prediction);
    }

    public function __toString(): string
    {
        return strval($this->content);
    }
}