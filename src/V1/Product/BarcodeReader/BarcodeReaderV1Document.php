<?php

declare(strict_types=1);

namespace Mindee\V1\Product\BarcodeReader;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Prediction;
use Mindee\V1\Parsing\Standard\StringField;
use Mindee\V1\Parsing\SummaryHelperV1;

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
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["codes_1d"])) {
            throw new MindeeUnsetException();
        }
        $this->codes1D = array_map(
            static fn($prediction) => new StringField($prediction, $pageId),
            $rawPrediction["codes_1d"]
        );
        if (!isset($rawPrediction["codes_2d"])) {
            throw new MindeeUnsetException();
        }
        $this->codes2D = array_map(
            static fn($prediction) => new StringField($prediction, $pageId),
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
        return SummaryHelperV1::cleanOutString($outStr);
    }
}
