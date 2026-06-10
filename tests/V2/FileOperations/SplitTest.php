<?php

declare(strict_types=1);

namespace V2\FileOperations;

use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\V2\FileOperations\Split;
use Mindee\V2\Product\Split\SplitResponse;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class SplitTest extends TestCase
{
    private string $splitDataDir;
    private string $finDocDataDir;

    protected function setUp(): void
    {
        $this->splitDataDir = TestingUtilities::getV2DataDir() . '/products/split';
        $this->finDocDataDir = TestingUtilities::getV2DataDir() . '/products/extraction/financial_document';
    }

    public function testProcessesSinglePageSplitCorrectly(): void
    {
        $inputSample = new PathInput($this->finDocDataDir . '/default_sample.jpg');

        $localResponse = new LocalResponse($this->splitDataDir . '/split_single.json');
        $doc = $localResponse->deserializeResponse(SplitResponse::class);

        $splitOperation = new Split($inputSample);
        $splits = $doc->inference->result->splits;
        $extractedSplits = $splitOperation->extractMultipleSplits(array_map(static fn($s) => $s->pageRange, $splits));

        self::assertCount(1, $extractedSplits);

        self::assertSame(1, $extractedSplits[0]->pageCount);
    }

    public function testProcessesMultiPageReceiptSplitCorrectly(): void
    {
        $inputSample = new PathInput($this->splitDataDir . '/invoice_5p.pdf');

        $localResponse = new LocalResponse($this->splitDataDir . '/split_multiple.json');
        $doc = $localResponse->deserializeResponse(SplitResponse::class);

        $splitOperation = new Split($inputSample);
        $splits = $doc->inference->result->splits;
        $extractedSplits = $splitOperation->extractMultipleSplits(array_map(static fn($s) => $s->pageRange, $splits));

        self::assertCount(3, $extractedSplits);

        self::assertSame(1, $extractedSplits[0]->pageCount);
        self::assertSame(3, $extractedSplits[1]->pageCount);
        self::assertSame(1, $extractedSplits[2]->pageCount);
    }
}
