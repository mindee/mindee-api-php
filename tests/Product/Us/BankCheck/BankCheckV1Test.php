<?php

namespace Product\Us\BankCheck;

use Mindee\Product\Us\BankCheck;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class BankCheckV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private Page $completePage0;
    private string $completeDocReference;
    private string $completePage0Reference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/bank_check/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(BankCheck\BankCheckV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(BankCheck\BankCheckV1::class, $emptyDocJSON["document"]);
        $this->completePage0 = new Page(BankCheck\BankCheckV1Page::class, $completeDocJSON["document"]["inference"]["pages"][0]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
        $this->completePage0Reference = file_get_contents($productDir . "summary_page0.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->date->value);
        $this->assertNull($prediction->amount->value);
        $this->assertEquals(0, count($prediction->payees));
        $this->assertNull($prediction->routingNumber->value);
        $this->assertNull($prediction->accountNumber->value);
        $this->assertNull($prediction->checkNumber->value);
    }
    public function testCompletePage0()
    {
        $this->assertEquals(0, $this->completePage0->id);
        $this->assertEquals($this->completePage0Reference, strval($this->completePage0));
    }
}
