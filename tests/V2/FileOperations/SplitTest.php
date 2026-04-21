<?php

namespace V2\FileOperations;

use Mindee\Input\LocalResponse;
use Mindee\Input\PathInput;
use Mindee\V2\FileOperations\Split;
use Mindee\V2\Product\Split\SplitResponse;
use PHPUnit\Framework\TestCase;

class SplitTest extends TestCase
{
    private string $splitDataDir;
    private string $finDocDataDir;

    protected function setUp(): void
    {
        $this->splitDataDir = \TestingUtilities::getV2DataDir() . '/products/split';
        $this->finDocDataDir = \TestingUtilities::getV2DataDir() . '/products/extraction/financial_document';
    }

    public function testProcessesSinglePageSplitCorrectly(): void
    {
        $inputSample = new PathInput($this->finDocDataDir . '/default_sample.jpg');

        $localResponse = new LocalResponse($this->splitDataDir . '/split_single.json');
        $doc = $localResponse->deserializeResponse(SplitResponse::class);

        $splitOperation = new Split($inputSample);
        $splits = $doc->inference->result->splits;
        $extractedSplits = $splitOperation->extractSplits(array_map(fn($s) => $s->pageRange, $splits));

        $this->assertCount(1, $extractedSplits);

        $this->assertEquals(1, $extractedSplits[0]->getPageCount());
    }

    public function testProcessesMultiPageReceiptSplitCorrectly(): void
    {
        $inputSample = new PathInput($this->splitDataDir . '/invoice_5p.pdf');

        $localResponse = new LocalResponse($this->splitDataDir . '/split_multiple.json');
        $doc = $localResponse->deserializeResponse(SplitResponse::class);

        $splitOperation = new Split($inputSample);
        $splits = $doc->inference->result->splits;
        $extractedSplits = $splitOperation->extractSplits(array_map(fn($s) => $s->pageRange, $splits));

        $this->assertCount(3, $extractedSplits);

        $this->assertEquals(1, $extractedSplits[0]->getPageCount());
        $this->assertEquals(3, $extractedSplits[1]->getPageCount());
        $this->assertEquals(1, $extractedSplits[2]->getPageCount());
    }
}
