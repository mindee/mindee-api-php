<?php

declare(strict_types=1);

namespace Mindee\V1\Parsing\Common;

use Mindee\Error\ErrorCode;
use Mindee\Error\MindeeApiException;
use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Extras\Extras;
use Stringable;

use function array_key_exists;
use function is_subclass_of;

/**
 * Base Page object for predictions.
 */
class Page implements Stringable
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
     * @var Prediction Type of Page prediction.
     */
    public Prediction $prediction;
    /**
     * @var Extras Potential Extras fields sent back along with the prediction.
     */
    public Extras $extras;

    /**
     * @param string $predictionType Type of prediction.
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     * @throws MindeeApiException Throws if the prediction type isn't recognized.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response, through the reflected document
     *                              class.
     */
    public function __construct(string $predictionType, array $rawPrediction)
    {
        $this->id = $rawPrediction['id'];
        if (!is_subclass_of($predictionType, Prediction::class)) {
            throw new MindeeApiException(
                "Invalid prediction type " . $predictionType . ", must extend " . Prediction::class,
                ErrorCode::INTERNAL_LIBRARY_ERROR
            );
        }
        $this->prediction = self::createPrediction($predictionType, $rawPrediction['prediction'], $this->id);
        if (array_key_exists('orientation', $rawPrediction)) {
            $this->orientation = new OrientationField($rawPrediction['orientation'], $this->id, false, 'value');
        }
        if (array_key_exists('extras', $rawPrediction) && $rawPrediction['extras']) {
            $this->extras = new Extras($rawPrediction['extras']);
        }
    }

    /**
     * @param class-string<Prediction> $predictionType Type of prediction.
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction array.
     */
    private static function createPrediction(string $predictionType, array $rawPrediction, int $pageId): Prediction
    {
        return new $predictionType($rawPrediction, $pageId);
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
