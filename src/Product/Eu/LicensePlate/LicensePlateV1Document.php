<?php

namespace Mindee\Product\Eu\LicensePlate;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\StringField;

/**
 * License Plate API version 1.1 document data.
 */
class LicensePlateV1Document extends Prediction
{
    /**
     * @var StringField[] List of all license plates found in the image.
     */
    public array $licensePlates;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["license_plates"])) {
            throw new MindeeUnsetException();
        }
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
