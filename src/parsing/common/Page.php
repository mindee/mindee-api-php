<?php

namespace Mindee\parsing\common;

class Page
{
    public int $id;
    public OrientationField $orientation;
    public Prediction $prediction;
    public $extras; // TODO: Extra.

    public function __construct(Prediction $prediction_type, array $raw_prediction)
    {
        $this->id = $raw_prediction['id'];
        if (array_key_exists('orientation', $raw_prediction)) {
            $this->orientation = new OrientationField($raw_prediction['orientation'], 'value', false, $this->id);
        }
        try {
            $this->prediction = new $prediction_type($raw_prediction['prediction'], $this->id);
        } catch (\Exception $e) {
            $this->prediction = new $prediction_type($raw_prediction['prediction']);
        }
        if (array_key_exists('extras', $raw_prediction) && $raw_prediction['extras']) {
            $this->extras = $raw_prediction['extras']; // TODO: Extra field.
        }
    }

    public function __toString(): string
    {
        $title = "Page $this->id";
        $dashes = str_repeat('-=', 10);

        return "$title
$dashes
$this->prediction";
    }
}
