<?php

namespace Mindee\V2\FileOperations;

use Mindee\Extraction\ExtractedImage;
use Mindee\Extraction\ImageExtractor;
use Mindee\Input\LocalInputSource;
use Mindee\V2\Product\Crop\CropItem;

/**
 * V2 Crop operation.
 */
class Crop
{
    /**
     * @var LocalInputSource localInputSource object
     */
    private readonly LocalInputSource $localInput;

    /**
     * @param LocalInputSource $localInput LocalInputSource object.
     */
    public function __construct(LocalInputSource $localInput)
    {
        $this->localInput = $localInput;
    }

    /**
     * Extracts a crop zone from a file.
     *
     * @param CropItem $crop Crop to extract.
     *
     * @return ExtractedImage extracted image
     */
    public function extractCrop(CropItem $crop): ExtractedImage
    {
        return $this->extractCrops([$crop])[0];
    }

    /**
     * Extracts multiple crop zones from a file.
     *
     * @param CropItem[] $crops List of crops to extract.
     * @return CropFiles list of extracted files
     */
    public function extractCrops(array $crops): CropFiles
    {
        $imageExtractor = new ImageExtractor($this->localInput);
        $extractedImages = [];

        $cropsPerPage = [];
        foreach ($crops as $crop) {
            $cropsPerPage[$crop->location->page][] = $crop;
        }

        foreach ($cropsPerPage as $page => $pageCrops) {
            $polygons = array_map(fn ($c) => $c->location->polygon, $pageCrops);
            $filenamePrefix = sprintf('%s_page%d', $this->localInput->fileName, $page);

            $images = $imageExtractor->extractPolygonsFromPage(
                $polygons,
                $page,
                $filenamePrefix
            );
            array_push($extractedImages, ...$images);
        }

        return new CropFiles(...$extractedImages);
    }
}
