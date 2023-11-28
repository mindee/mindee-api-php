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
     * @param array        $raw_prediction Raw prediction array.
     * @param integer|null $page_id        Page number for multi pages PDF.
     */
    public function __construct(array $raw_prediction, ?int $page_id = null)
    {
        $this->croppings = [];
        if (array_key_exists("cropping", $raw_prediction)) {
            foreach ($raw_prediction['cropping'] as $cropping) {
                $this->croppings[] = new PositionField($cropping, $page_id);
            }
        }
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $croppings_str = [];
        foreach ($this->croppings as $cropping) {
            $croppings_str[] = strval($cropping);
        }
        return implode("\n           ", $croppings_str);
    }
}
