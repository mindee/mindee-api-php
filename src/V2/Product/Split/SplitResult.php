<?php

declare(strict_types=1);

namespace Mindee\V2\Product\Split;

use Mindee\Input\LocalInputSource;
use Mindee\Pdf\ExtractedPdf;
use Mindee\V2\FileOperations\Split;
use Mindee\V2\FileOperations\SplitFiles;
use Stringable;
use ImagickException;

/**
 * Result of a split utility inference.
 */
class SplitResult implements Stringable
{
    /**
     * @var SplitRange[] A single document as identified when splitting a multi-document source file.
     */
    public array $splits;

    /**
     * @param array<string, int|float|string|bool|null|array<array-key, mixed>> $rawResponse Raw server response array.
     */
    public function __construct(array $rawResponse)
    {
        $this->splits = array_map(static fn($split) => new SplitRange($split), $rawResponse['splits']);
    }

    /**
     * @return string String representation.
     */
    public function __toString(): string
    {
        $splitsStr = implode("\n", $this->splits);

        return "Splits\n======\n$splitsStr";
    }

    /**
     * @param LocalInputSource $inputSource The input source from which to extract the pages.
     * @return SplitFiles The extracted PDFs.
     * @throws ImagickException Throws if the image can't be processed.
     */
    public function extractFromInputSource(LocalInputSource $inputSource): SplitFiles
    {
        $splitter = new Split($inputSource);
        return $splitter->extractMultipleSplits(array_map(static fn(SplitRange $split) => $split->pageRange, $this->splits));
    }
}
