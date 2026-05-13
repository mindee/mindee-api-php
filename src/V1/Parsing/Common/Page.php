<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeApiException;
use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Extras\Extras;
use ReflectionClass;
use ReflectionException;

use function array_key_exists;

/**
 * Base Page object for predictions.
 */
class Page
{
    /**
     * @var integer ID of the current page.
     */
    public int $id;
    /**
     * @var OrientationField Orientation of the page.
     */
    public OrientationField $orientation;
    /**
     * @var Prediction|object Type of Page prediction.
     */
    public mixed $prediction;
    /**
     * @var Extras Potential Extras fields sent back along with the prediction.
     */
    public Extras $extras;

    /**
     * @param string $predictionType Type of prediction.
     * @param array<string, mixed> $rawPrediction Raw prediction array.
     * @throws MindeeApiException Throws if the prediction type isn't recognized.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response, through the reflected document
     *                              class.
     */
    public function __construct(string $predictionType, array $rawPrediction)
    {
        $this->id = $rawPrediction['id'];
        try {
            $reflection = new ReflectionClass($predictionType);
            $this->prediction = $reflection->newInstance($rawPrediction['prediction'], $this->id);
        } catch (ReflectionException $e) {
            throw new MindeeApiException(
                "Unable to create custom product " . $predictionType,
                ErrorCode::INTERNAL_LIBRARY_ERROR,
                $e
            );
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
        $dashes = str_repeat('-', mb_strlen($title, "UTF-8"));

        return "$title
$dashes
$this->prediction";
    }
}
