<?php

declare(strict_types=1);

namespace Mindee\V1\Product\Cropper;

use Mindee\V1\Parsing\Standard\PositionField;
use Mindee\V1\Parsing\SummaryHelperV1;

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
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawPrediction Raw prediction from HTTP response.
     * @param integer|null $pageId Page number for multi pages document.
     */
    public function __construct(array $rawPrediction, ?int $pageId = null)
    {
        $this->cropping = null === $rawPrediction["cropping"] ? [] : array_map(
            static fn($prediction) => new PositionField($prediction, $pageId),
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
        return SummaryHelperV1::cleanOutString($outStr);
    }
}
