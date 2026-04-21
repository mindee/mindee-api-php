<?php

namespace Mindee\V2\FileOperations;

use Mindee\Error\MindeeException;
use Mindee\Extraction\ExtractedImage;

/**
 * Cropped files collection wrapper.
 *
 * * @extends \ArrayObject<int, ExtractedImage>
 */
class CropFiles extends \ArrayObject
{
    /**
     * Builds a new CropFiles collection.
     *
     * @param ExtractedImage ...$items Items.
     */
    public function __construct(ExtractedImage ...$items)
    {
        parent::__construct($items);
    }

    /**
     * Save all extracted crops to disk.
     *
     * @param string      $path       The directory path to save the extracted crops to.
     * @param string      $prefix     Prefix to add to the filename.
     * @param null|string $fileFormat File format to save the crops as.
     * @param integer     $quality    Quality of the saved image.
     *
     * @throws MindeeException If directory creation fails.
     * @return void
     */
    public function saveAllToDisk(
        string $path,
        string $prefix = 'crop',
        ?string $fileFormat = null,
        int $quality = 100
    ): void {
        $format ??= $fileFormat;
        $idx = 1;

        foreach ($this as $crop) {
            $formattedIdx = sprintf('%03d', $idx);
            $filename = sprintf('%s_%s.jpg', $prefix, $formattedIdx);
            $crop->filename = $filename;

            try {
                $crop->writeToFile($path, $format, $quality);
            } catch (\ImagickException $e) {
                throw new MindeeException('Failed to save crop to disk.', 0, $e);
            }

            ++$idx;
        }
    }
}
