<?php

namespace Mindee\V2\FileOperations;

use Mindee\Error\MindeeInputException;
use Mindee\Extraction\ExtractedPdf;
use Mindee\Extraction\PdfExtractor;
use Mindee\Input\LocalInputSource;

/**
 * V2 Split operation.
 */
class Split
{
    /**
     * @var LocalInputSource localInputSource object
     */
    private readonly LocalInputSource $localInput;

    /**
     * @param LocalInputSource $inputSource localInputSource object
     */
    public function __construct(LocalInputSource $inputSource)
    {
        $this->localInput = $inputSource;
    }

    /**
     * Expands a range to a list of integers.
     *
     * @param int $start start of the range
     * @param int $end   end of the range
     *
     * @return int[]
     *
     * @throws MindeeInputException if the start page is greater than the end page
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
     * @param int[] $split split range to extract
     *
     * @return ExtractedPdf 2D array of extracted pages
     */
    public function extractSingleSplit(array $split): ExtractedPdf
    {
        return $this->extractSplits([$split])[0];
    }

    /**
     * Extracts the splits from the input file.
     *
     * @param int[][] $splits list of split ranges to extract
     *
     * @return SplitFiles list of extracted files
     */
    public function extractSplits(array $splits): SplitFiles
    {
        $pdfExtractor = new PdfExtractor($this->localInput);
        $expandedPageIndexes = array_map(fn (array $split) => self::expandRange($split[0], $split[1]), $splits);

        return new SplitFiles(...$pdfExtractor->extractSubDocuments($expandedPageIndexes));
    }
}
