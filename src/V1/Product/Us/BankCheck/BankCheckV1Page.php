<?php

namespace Mindee\V1\Product\Us\BankCheck;

use Mindee\V1\Parsing\Standard\PositionField;
use Mindee\V1\Parsing\SummaryHelperV1;

/**
 * Bank Check API version 1.1 page data.
 */
class BankCheckV1Page extends BankCheckV1Document
{
    /**
     * @var PositionField The position of the check on the document.
     */
    public PositionField $checkPosition;
    /**
     * @var PositionField[] List of signature positions
     */
    public array $signaturesPositions;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        parent::__construct($rawPrediction, $pageId);
        $this->checkPosition = new PositionField(
            $rawPrediction["check_position"],
            $pageId
        );
        $this->signaturesPositions = $rawPrediction["signatures_positions"] == null ? [] : array_map(
            fn ($prediction) => new PositionField($prediction, $pageId),
            $rawPrediction["signatures_positions"]
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $signaturesPositions = implode(
            "\n                      ",
            $this->signaturesPositions
        );

        $outStr = ":Check Position: $this->checkPosition
:Signature Positions: $signaturesPositions
";
        $outStr .= parent::__toString();
        return SummaryHelperV1::cleanOutString($outStr);
    }
}
