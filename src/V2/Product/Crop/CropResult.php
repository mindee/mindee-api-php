<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Crop;

use Mindee\Input\LocalInputSource;
use Mindee\V2\FileOperations\Crop;
use Mindee\V2\FileOperations\CropFiles;
use Stringable;

/**
 * Result of a crop utility inference.
 */
class CropResult implements Stringable
{
    /**
     * @var CropItem[] Crops extracted from the image.
     */
    public array $crops;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $cropsOnPage = [];
        $this->crops = array_map(
            static function (array $crop) use (&$cropsOnPage): CropItem {
                $page = $crop['location']['page'];
                $indexOnPage = $cropsOnPage[$page] ?? 0;
                $cropsOnPage[$page] = $indexOnPage + 1;

                return new CropItem($crop, $indexOnPage);
            },
            $rawResponse['crops']
        );
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        return "Crops\n=====\n" . implode("\n", $this->crops);
    }

    /**
     * @param LocalInputSource $inputSource The input source from which to extract the pages.
     * @return CropFiles The extracted PDFs.
     */
    public function extractFromInputSource(LocalInputSource $inputSource): CropFiles
    {
        $cropper = new Crop($inputSource);
        return $cropper->extractMultipleCrops($this->crops);
    }
}
