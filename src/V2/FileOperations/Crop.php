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
     * Extracts a crop zone from a file, named "<file>_page<page>-<index of the crop on its page>".
     *
     * @param CropItem $crop Crop to extract.
     *
     * @return ExtractedImage extracted image
     */
    public function extractSingleCrop(CropItem $crop): ExtractedImage
    {
        return $this->extractMultipleCrops([$crop])[0];
    }

    /**
     * Extracts multiple crop zones from a file, rendering each page once.
     *
     * Each image is named "<file>_page<page>-<index of the crop on its page>". Crops parsed from an inference
     * know that index; for the others, crops are numbered per page in the order given.
     *
     * @param CropItem[] $crops List of crops to extract.
     * @return CropFiles list of extracted files, in the same order as the crops
     */
    public function extractMultipleCrops(array $crops): CropFiles
    {
        $crops = array_values($crops);
        $imageExtractor = new ImageExtractor($this->localInput);

        $cropsPerPage = [];
        foreach ($crops as $position => $crop) {
            $cropsPerPage[$crop->location->page][$position] = $crop;
        }

        /** @var array<int, ExtractedImage> $extractedImages keyed by position in $crops */
        $extractedImages = [];
        foreach ($cropsPerPage as $page => $pageCrops) {
            $usedIndexes = array_map(static fn(CropItem $crop): ?int => $crop->indexOnPage, $pageCrops);
            $nextIndex = 0;
            $indexes = [];
            foreach ($pageCrops as $crop) {
                if ($crop->indexOnPage !== null) {
                    $indexes[] = $crop->indexOnPage;
                    continue;
                }
                while (in_array($nextIndex, $usedIndexes, true)) {
                    ++$nextIndex;
                }
                $indexes[] = $nextIndex;
                $usedIndexes[] = $nextIndex++;
            }
            $images = $imageExtractor->extractMultipleImagesFromSource(
                array_map(static fn(CropItem $c) => $c->location->polygon, array_values($pageCrops)),
                $page,
                sprintf('%s_page%d', $this->localInput->fileName, $page),
                null,
                $indexes
            );
            foreach (array_keys($pageCrops) as $i => $position) {
                $extractedImages[$position] = $images[$i];
            }
        }
        ksort($extractedImages);

        return new CropFiles(...array_values($extractedImages));
    }
}
