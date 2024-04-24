<?php

namespace Mindee\Product\Cropper;

use Mindee\Parsing\Common\SummaryHelper;
use Mindee\Parsing\Standard\PositionField;

/**
 * Cropper API version 1.1 page data.
 */
class CropperV1Page extends CropperV1Document
{
    /**
     * @var PositionField[] List of documents found in the image.
     */
    public array $cropping;
    /**
     * @param array        $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId        Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->cropping = $rawPrediction["cropping"] == null ? [] : array_map(
            fn ($prediction) => new PositionField($prediction, $pageId),
            $rawPrediction["cropping"]
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $cropping = implode(
            "\n                   ",
            $this->cropping
        );

        $outStr = ":Document Cropper: $cropping
";
        $outStr .= parent::__toString();
        return SummaryHelper::cleanOutString($outStr);
    }
}
