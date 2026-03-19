<?php

namespace Mindee\V2\Product\Crop;

/**
 * Result of a crop utility inference.
 */
class CropResult
{
    /**
     * @var CropItem[] Crops extracted from the image.
     */
    public array $crops;

    /**
     * @param array $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->crops = array_map(fn ($crop) => new CropItem($crop), $rawResponse['crops']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Crops\n=====\n" . implode("\n", $this->crops);
    }
}
