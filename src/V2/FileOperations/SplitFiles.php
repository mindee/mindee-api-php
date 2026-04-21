<?php

namespace Mindee\V2\FileOperations;

use Mindee\Error\MindeeException;
use Mindee\Extraction\ExtractedPdf;

/**
 * Split files collection wrapper.
 *
 * * @extends \ArrayObject<int, ExtractedPdf>
 */
class SplitFiles extends \ArrayObject
{
    /**
     * Builds a new SplitFiles collection.
     *
     * @param ExtractedPdf ...$items Items.
     */
    public function __construct(ExtractedPdf ...$items)
    {
        parent::__construct($items);
    }

    /**
     * Save all extracted splits to disk.
     *
     * @param string $path   the directory path to save the extracted splits to
     * @param string $prefix prefix to add to the filename
     *
     * @throws MindeeException if directory creation fails
     */
    public function saveAllToDisk(string $path, string $prefix = 'split'): void
    {
        if (!is_dir($path)) {
            if (!mkdir($path, 0o777, true) && !is_dir($path)) {
                throw new MindeeException(sprintf('Directory "%s" was not created', $path));
            }
        }

        $idx = 1;

        foreach ($this as $split) {
            $formattedIdx = sprintf('%03d', $idx);
            $filename = sprintf('%s_%s.pdf', $prefix, $formattedIdx);
            $filePath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

            try {
                $split->writeToFile($filePath);
            } catch (\Exception $e) {
                throw new MindeeException('Failed to save split to disk.', 0, $e->getMessage());
            }

            ++$idx;
        }
    }
}
