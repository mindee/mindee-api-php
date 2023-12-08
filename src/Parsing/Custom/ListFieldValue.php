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
     * @var integer|null Page number for multi pages document.
     */
    private ?int $pageId;


    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(
        array $rawPrediction,
        ?int $pageId = null
    ) {
        $this->content = $rawPrediction['content'];
        $this->confidence = $rawPrediction['confidence'];
        $this->pageId = $pageId;
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
