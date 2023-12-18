<?php

namespace Mindee\Parsing\Common\Extras;

use Mindee\Parsing\Standard\PositionField;

/**
 * Contains information on the cropping of a prediction.
 */
class CropperExtra
{
    /**
     * @var array List of all croppings coordiantes.
     */
    public array $croppings;

    /**
     * @param array        $rawPrediction Raw prediction array.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->croppings = [];
        if (array_key_exists("cropping", $rawPrediction)) {
            foreach ($rawPrediction['cropping'] as $cropping) {
                $this->croppings[] = new PositionField($cropping, $pageId);
            }
        }
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $croppingsStr = [];
        foreach ($this->croppings as $cropping) {
            $croppingsStr[] = strval($cropping);
        }
        return implode("\n           ", $croppingsStr);
    }
}
