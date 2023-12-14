<?php

namespace Mindee\Product\MultiReceiptsDetector;

use Mindee\Parsing\Common\Prediction;
use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\PositionField;

/**
 * Document data for Multi Receipts Detector, API version 1.
 */
class MultiReceiptsDetectorV1Document extends Prediction
{
    /**
    * @var PositionField[]|null Positions of the receipts on the document.
    */
    public ?array $receipts;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
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
