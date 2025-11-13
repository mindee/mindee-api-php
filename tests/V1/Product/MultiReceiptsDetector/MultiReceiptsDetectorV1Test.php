<?php

namespace V1\Product\MultiReceiptsDetector;

use Mindee\Parsing\Common\Document;
use Mindee\Product\MultiReceiptsDetector;
use PHPUnit\Framework\TestCase;

class MultiReceiptsDetectorV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = \TestingUtilities::getV1DataDir() . "/products/multi_receipts_detector/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(MultiReceiptsDetector\MultiReceiptsDetectorV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(MultiReceiptsDetector\MultiReceiptsDetectorV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertEquals(0, count($prediction->receipts));
    }
}
