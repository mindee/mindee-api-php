<?php

namespace Product\BarcodeReader;

use Mindee\Product\BarcodeReader;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class BarcodeReaderV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/barcode_reader/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(BarcodeReader\BarcodeReaderV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(BarcodeReader\BarcodeReaderV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertEquals(0, count($prediction->codes1D));
        $this->assertEquals(0, count($prediction->codes2D));
    }
}
