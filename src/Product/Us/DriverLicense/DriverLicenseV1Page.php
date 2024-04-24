<?php

namespace Mindee\Product\Us\DriverLicense;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\PositionField;

/**
 * Driver License API version 1.1 page data.
 */
class DriverLicenseV1Page extends DriverLicenseV1Document
{
    /**
     * @var PositionField Has a photo of the US driver license holder
     */
    public PositionField $photo;
    /**
     * @var PositionField Has a signature of the US driver license holder
     */
    public PositionField $signature;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        parent::__construct($rawPrediction, $pageId);
        $this->photo = new PositionField(
            $rawPrediction["photo"],
            $pageId
        );
        $this->signature = new PositionField(
            $rawPrediction["signature"],
            $pageId
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {

        $outStr = ":Photo: $this->photo
:Signature: $this->signature
";
        $outStr .= parent::__toString();
        return SummaryHelper::cleanOutString($outStr);
    }
}
