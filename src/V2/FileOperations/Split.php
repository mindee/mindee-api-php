<?php

declare(strict_types=1);

namespace Mindee\V2\FileOperations;

use Mindee\Error\MindeeInputException;
use Mindee\Input\LocalInputSource;
use Mindee\Pdf\ExtractedPdf;
use Mindee\Pdf\PdfExtractor;
use ImagickException;

/**
 * V2 Split operation.
 */
class Split
{
    /**
     * @param LocalInputSource $localInput LocalInputSource object.
     */
    public function __construct(private readonly LocalInputSource $localInput) {}

    /**
     * Expands a range to a list of integers.
     *
     * @param integer $start Start of the range.
     * @param integer $end End of the range.
     *
     * @return int[]
     *
     * @throws MindeeInputException If the start page is greater than the end page.
     */
    public static function expandRange(int $start, int $end): array
    {
        if ($start > $end || $start < 0) {
            throw new MindeeInputException('Invalid page range provided.');
        }

        return range($start, $end);
    }

    /**
     * Extracts a single split from the input file.
     *
     * @param int[] $split Split range to extract.
     *
     * @return ExtractedPdf 2D array of extracted pages
     * @throws ImagickException Throws if the image can't be processed.
     */
    public function extractSingleSplit(array $split): ExtractedPdf
    {
        return $this->extractMultipleSplits([$split])[0];
    }

    /**
     * Extracts the splits from the input file.
     *
     * @param int[][] $splits List of split ranges to extract.
     *
     * @return SplitFiles list of extracted files
     * @throws ImagickException Throws if the image can't be processed.
     */
    public function extractMultipleSplits(array $splits): SplitFiles
    {
        $pdfExtractor = new PdfExtractor($this->localInput);
        $expandedPageIndexes = array_map(static fn(array $split) => self::expandRange($split[0], $split[1]), $splits);

        return new SplitFiles(...$pdfExtractor->extractSubDocuments($expandedPageIndexes));
    }
}
