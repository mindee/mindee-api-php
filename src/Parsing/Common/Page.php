<?php

namespace Mindee\Parsing\Common;

use Mindee\Error\MindeeApiException;
use Mindee\Parsing\Common\Extras\Extras;

/**
 * Base Page object for predictions.
 */
class Page
{
    /**
     * @var integer|mixed ID of the current page.
     */
    public int $id;
    /**
     * @var \Mindee\Parsing\Common\OrientationField Orientation of the page.
     */
    public OrientationField $orientation;
    /**
     * @var \Mindee\Parsing\Common\Prediction|object Type of Page prediction.
     */
    public Prediction $prediction;
    /**
     * @var \Mindee\Parsing\Common\Extras\Extras Potential Extras fields sent back along with the prediction.
     */
    public Extras $extras;

    /**
     * @param string $prediction_type Type of prediction.
     * @param array  $raw_prediction  Raw prediction array.
     * @throws \Mindee\Error\MindeeApiException Throws if the prediction type isn't recognized.
     */
    public function __construct(string $prediction_type, array $raw_prediction)
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

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $title = "Page $this->id";
        $dashes = str_repeat('-', strlen($title));

        return "$title
$dashes
$this->prediction
";
    }
}
