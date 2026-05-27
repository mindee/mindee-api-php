<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common\Ocr;

use Mindee\V1\Parsing\Standard\FieldPositionMixin;
use Stringable;

/**
 * A single word.
 */
class OcrWord implements Stringable
{
    use FieldPositionMixin;

    /**
     * @var float The confidence score.
     */
    public float $confidence;

    /**
     * @var string The extracted text.
     */
    public string $text;


    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
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
