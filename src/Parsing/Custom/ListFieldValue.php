<?php

namespace Mindee\Parsing\Custom;

use Mindee\Parsing\Standard\FieldPositionMixin;

/**
 * A single Value or word.
 */
class ListFieldValue
{
    use FieldPositionMixin;

    /**
     * @var string|mixed The content text.
     */
    public string $content;

    /**
     * @var float|mixed Confidence score.
     */
    public float $confidence;


    /**
     * @param array $raw_prediction Raw prediction array.
     */
    public function __construct(
        array $raw_prediction
    ) {
        $this->content    = $raw_prediction['content'];
        $this->confidence = $raw_prediction['confidence'];
        $this->setPosition($raw_prediction);
    }


    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return strval($this->content);
    }
}
