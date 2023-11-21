<?php

namespace Mindee\Parsing\Common;

use Mindee\Error\MindeeApiException;
use Mindee\Parsing\Common\Extras\Extras;

class Page
{
    public int $id;
    public OrientationField $orientation;
    public Prediction $prediction;
    public Extras $extras;

    public function __construct($prediction_type, array $raw_prediction)
    {
        $this->id = $raw_prediction['id'];
        try {
            $reflection = new \ReflectionClass($prediction_type);
            $this->prediction = $reflection->newInstance($raw_prediction['prediction'], $this->id);
        } catch (\ReflectionException $exception) {
            throw new MindeeApiException("Unable to create custom product " . $prediction_type);
        }
        if (array_key_exists('orientation', $raw_prediction)) {
            $this->orientation = new OrientationField($raw_prediction['orientation'], $this->id, false, 'value');
        }
        if (array_key_exists('extras', $raw_prediction) && $raw_prediction['extras']) {
            $this->extras = new Extras($raw_prediction['extras']);
        }
    }

    public function __toString(): string
    {
        $title = "Page $this->id";
        $dashes = str_repeat('-', strlen($title));

        return "$title
$dashes
$this->prediction";
    }
}
