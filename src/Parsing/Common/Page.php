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
     * @param string $predictionType Type of prediction.
     * @param array  $rawPrediction  Raw prediction array.
     * @throws \Mindee\Error\MindeeApiException Throws if the prediction type isn't recognized.
     */
    public function __construct(string $predictionType, array $rawPrediction)
    {
        $this->id = $rawPrediction['id'];
        try {
            $reflection = new \ReflectionClass($predictionType);
            $this->prediction = $reflection->newInstance($rawPrediction['prediction'], $this->id);
        } catch (\ReflectionException $exception) {
            throw new MindeeApiException("Unable to create custom product " . $predictionType);
        }
        if (array_key_exists('orientation', $rawPrediction)) {
            $this->orientation = new OrientationField($rawPrediction['orientation'], $this->id, false, 'value');
        }
        if (array_key_exists('extras', $rawPrediction) && $rawPrediction['extras']) {
            $this->extras = new Extras($rawPrediction['extras']);
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
$this->prediction";
    }
}
