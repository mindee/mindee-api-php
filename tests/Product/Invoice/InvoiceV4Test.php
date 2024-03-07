<?php

namespace Product\Invoice;

use Mindee\Product\Invoice;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class InvoiceV4Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/invoices/response_v4/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(Invoice\InvoiceV4::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(Invoice\InvoiceV4::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->locale->value);
        $this->assertNull($prediction->invoiceNumber->value);
        $this->assertEquals(0, count($prediction->referenceNumbers));
        $this->assertNull($prediction->date->value);
        $this->assertNull($prediction->dueDate->value);
        $this->assertNull($prediction->totalNet->value);
        $this->assertNull($prediction->totalAmount->value);
        $this->assertNull($prediction->totalTax->value);
        $this->assertEquals(0, count($prediction->taxes));
        $this->assertEquals(0, count($prediction->supplierPaymentDetails));
        $this->assertNull($prediction->supplierName->value);
        $this->assertEquals(0, count($prediction->supplierCompanyRegistrations));
        $this->assertNull($prediction->supplierAddress->value);
        $this->assertNull($prediction->customerName->value);
        $this->assertEquals(0, count($prediction->customerCompanyRegistrations));
        $this->assertNull($prediction->customerAddress->value);
        $this->assertNull($prediction->shippingAddress->value);
        $this->assertNull($prediction->billingAddress->value);
        $this->assertEquals(0, count($prediction->lineItems));
    }
}
