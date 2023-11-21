<?php

namespace Mindee\Parsing\Common\Extras;

use Mindee\Parsing\Standard\PositionField;

class CropperExtra
{
    public array $croppings;

    public function __construct(array $raw_prediction, ?int $page_id = null)
    {
        $this->croppings = [];
        if (array_key_exists("cropping", $raw_prediction)) {
            foreach ($raw_prediction['cropping'] as $cropping) {
                $this->croppings[] = new PositionField($cropping, $page_id);
            }
        }
    }

    public function __toString(): string
    {
        $croppings_str = [];
        foreach ($this->croppings as $cropping) {
            $croppings_str[] = strval($cropping);
        }
        return implode("\n           ", $croppings_str);
    }
}
