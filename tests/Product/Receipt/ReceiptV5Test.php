<?php

namespace Product\Receipt;

use Mindee\Product\Receipt;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class ReceiptV5Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private Page $completePage0;
    private string $completeDocReference;
    private string $completePage0Reference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/expense_receipts/response_v5/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(Receipt\ReceiptV5::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(Receipt\ReceiptV5::class, $emptyDocJSON["document"]);
        $this->completePage0 = new Page(Receipt\ReceiptV5Document::class, $completeDocJSON["document"]["inference"]["pages"][0]);
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
        $this->assertNull($prediction->locale->value);
        $this->assertNull($prediction->date->value);
        $this->assertNull($prediction->time->value);
        $this->assertNull($prediction->totalAmount->value);
        $this->assertNull($prediction->totalNet->value);
        $this->assertNull($prediction->totalTax->value);
        $this->assertNull($prediction->tip->value);
        $this->assertEquals(0, count($prediction->taxes));
        $this->assertNull($prediction->supplierName->value);
        $this->assertEquals(0, count($prediction->supplierCompanyRegistrations));
        $this->assertNull($prediction->supplierAddress->value);
        $this->assertNull($prediction->supplierPhoneNumber->value);
        $this->assertEquals(0, count($prediction->lineItems));
    }

    public function testCompletePage0()
    {
        $this->assertEquals(0, $this->completePage0->id);
        $this->assertEquals($this->completePage0Reference, strval($this->completePage0));
    }

}
