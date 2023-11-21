<?php

namespace Mindee\Parsing\Common\Extras;

use Mindee\Parsing\Standard\PositionField;

class CropperExtra
{
    public array $croppings;

    function __construct(array $raw_prediction, ?int $page_id = null)
    {
        $croppings = [];
        if (array_key_exists("cropping", $raw_prediction)) {
            foreach ($raw_prediction['cropping'] as $cropping) {
                $croppings[] = new PositionField($cropping, $page_id);
            }
        }
        $this->$croppings = $croppings;
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
