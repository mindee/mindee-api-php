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
     * @param array $rawPrediction Raw prediction array.
     */
    public function __construct(
        array $rawPrediction
    ) {
        $this->content    = $rawPrediction['content'];
        $this->confidence = $rawPrediction['confidence'];
        $this->setPosition($rawPrediction);
    }


    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return strval($this->content);
    }
}
