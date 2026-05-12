<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common\OCR;

use Mindee\V1\Parsing\Standard\FieldPositionMixin;

/**
 * A single word.
 */
class OCRWord
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
