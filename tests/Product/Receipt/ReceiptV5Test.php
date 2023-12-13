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
        $prediction = $this->emptyDoc->inference->prediction;
        $prediction = $this->emptyDoc->inference->prediction;
        $prediction = $this->emptyDoc->inference->prediction;
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->date->value);
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->time->value);
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->totalAmount->value);
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->totalNet->value);
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->totalTax->value);
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->tip->value);
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertEquals(0, count($prediction->taxes));
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->supplierName->value);
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertEquals(0, count($prediction->supplierCompanyRegistrations));
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->supplierAddress->value);
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->supplierPhoneNumber->value);
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertEquals(0, count($prediction->lineItems));
    }
}
