<?php

namespace Mindee\Product\MultiReceiptsDetector;

use Mindee\Error\MindeeUnsetException;
use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\PositionField;

/**
 * Multi Receipts Detector API version 1.0 document data.
 */
class MultiReceiptsDetectorV1Document extends Prediction
{
    /**
     * @var PositionField[] Positions of the receipts on the document.
     */
    public array $receipts;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["receipts"])) {
            throw new MindeeUnsetException();
        }
        $this->receipts = $rawPrediction["receipts"] == null ? [] : array_map(
            fn ($prediction) => new PositionField($prediction, $pageId),
            $rawPrediction["receipts"]
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $receipts = implode(
            "\n                   ",
            $this->receipts
        );

        $outStr = ":List of Receipts: $receipts
";
        return SummaryHelper::cleanOutString($outStr);
    }
}
