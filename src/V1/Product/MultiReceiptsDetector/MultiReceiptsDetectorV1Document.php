<?php

declare(strict_types=1);

namespace Mindee\V1\Product\MultiReceiptsDetector;

use Mindee\Error\MindeeUnsetException;
use Mindee\V1\Parsing\Common\Prediction;
use Mindee\V1\Parsing\Standard\PositionField;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Multi Receipts Detector API version 1.1 document data.
 */
class MultiReceiptsDetectorV1Document extends Prediction
{
    /**
     * @var PositionField[] Positions of the receipts on the document.
     */
    public array $receipts;
    /**
     * @param array<string, mixed> $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId Page number for multi pages document.
     * @throws MindeeUnsetException Throws if a field doesn't appear in the response.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        if (!isset($rawPrediction["receipts"])) {
            throw new MindeeUnsetException();
        }
        $this->receipts = $rawPrediction["receipts"] == null ? [] : array_map(
            static fn($prediction) => new PositionField($prediction, $pageId),
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
        return SummaryHelperV1::cleanOutString($outStr);
    }
}
