<?php

namespace Mindee\Product\Us\BankCheck;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\PositionField;

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
        return SummaryHelper::cleanOutString($outStr);
    }
}
