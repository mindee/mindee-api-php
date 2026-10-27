<?php

declare(strict_types=1);

namespace Mindee\V2\FileOperations;

use Mindee\Image\ExtractedImage;
use Mindee\Image\ImageExtractor;
use Mindee\Input\LocalInputSource;
use Mindee\V2\Product\Crop\CropItem;

use function sprintf;

/**
 * V2 Crop operation.
 */
class Crop
{
    /**
     * @param LocalInputSource $localInput LocalInputSource object.
     */
    public function __construct(private readonly LocalInputSource $localInput) {}

    /**
     * Extracts a crop zone from a file.
     *
     * @param CropItem $crop Crop to extract.
     * @param integer $index Position of the crop among the inference's crops: its file name is
     *                       "<file>_page<page>-<index>", so crops extracted one by one need
     *                       different indexes not to overwrite each other.
     *
     * @return ExtractedImage extracted image
     */
    public function extractSingleCrop(CropItem $crop, int $index = 0): ExtractedImage
    {
        return $this->extractCrops([$index => $crop])[0];
    }

    /**
     * Extracts multiple crop zones from a file.
     *
     * @param CropItem[] $crops List of crops to extract.
     * @return CropFiles list of extracted files, in the same order as the crops; each is named
     *                   "<file>_page<page>-<position in the list>".
     */
    public function extractMultipleCrops(array $crops): CropFiles
    {
        return new CropFiles(...$this->extractCrops(array_values($crops)));
    }

    /**
     * Extracts crops, rendering each page once.
     *
     * @param array<int, CropItem> $crops Crops to extract, keyed by the index their file is named after.
     * @return list<ExtractedImage> extracted images, in the same order as the crops
     */
    private function extractCrops(array $crops): array
    {
        $imageExtractor = new ImageExtractor($this->localInput);

        $cropsPerPage = [];
        foreach ($crops as $index => $crop) {
            $cropsPerPage[$crop->location->page][$index] = $crop;
        }

        $extractedImages = [];
        foreach ($cropsPerPage as $page => $pageCrops) {
            $indexes = array_keys($pageCrops);
            $images = $imageExtractor->extractMultipleImagesFromSource(
                array_map(static fn(CropItem $c) => $c->location->polygon, array_values($pageCrops)),
                $page,
                sprintf('%s_page%d', $this->localInput->fileName, $page),
                null,
                $indexes
            );
            foreach ($indexes as $i => $index) {
                $extractedImages[$index] = $images[$i];
            }
        }

        $ordered = [];
        foreach (array_keys($crops) as $index) {
            $ordered[] = $extractedImages[$index];
        }

        return $ordered;
    }
}
