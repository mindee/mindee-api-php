<?php

namespace Mindee\Product\Eu\LicensePlate;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\StringField;

/**
 * Document data for License Plate, API version 1.
 */
class LicensePlateV1Document extends Prediction
{
    /**
    * @var StringField[]|null List of all license plates found in the image.
    */
    public ?array $licensePlates;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->licensePlates = $rawPrediction["license_plates"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["license_plates"]
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $licensePlates = implode(
            "\n                 ",
            $this->licensePlates
        );

        $outStr = ":License Plates: $licensePlates
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
