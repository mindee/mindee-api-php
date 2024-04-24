<?php

namespace Mindee\Product\BarcodeReader;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\StringField;

/**
 * Barcode Reader API version 1.0 document data.
 */
class BarcodeReaderV1Document extends Prediction
{
    /**
     * @var StringField[] List of decoded 1D barcodes.
     */
    public array $codes1D;
    /**
     * @var StringField[] List of decoded 2D barcodes.
     */
    public array $codes2D;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["codes_1d"])) {
            throw new MindeeUnsetException();
        }
        $this->codes1D = $rawPrediction["codes_1d"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["codes_1d"]
        );
        if (!isset($rawPrediction["codes_2d"])) {
            throw new MindeeUnsetException();
        }
        $this->codes2D = $rawPrediction["codes_2d"] == null ? [] : array_map(
            fn ($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["codes_2d"]
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $codes1D = implode(
            "\n              ",
            $this->codes1D
        );
        $codes2D = implode(
            "\n              ",
            $this->codes2D
        );

        $outStr = ":Barcodes 1D: $codes1D
:Barcodes 2D: $codes2D
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
