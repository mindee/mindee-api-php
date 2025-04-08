<?php

namespace Product\FinancialDocument;

use Mindee\Product\FinancialDocument;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class FinancialDocumentV1Test extends TestCase
{
    private Document $completeDocInvoice;
    private Document $completeDocReceipt;
    private Document $emptyDoc;
    private Page $completePage0Invoice;
    private Page $completePage0Receipt;
    private string $completeDocReferenceInvoice;
    private string $completeDocReferenceReceipt;
    private string $completePage0ReferenceInvoice;
    private string $completePage0ReferenceReceipt;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/financial_document/response_v1/";
        $completeDocFileInvoice = file_get_contents($productDir . "complete_invoice.json");
        $completeDocFileReceipt = file_get_contents($productDir . "complete_receipt.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSONInvoice = json_decode($completeDocFileInvoice, true);
        $completeDocJSONReceipt = json_decode($completeDocFileReceipt, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDocInvoice = new Document(FinancialDocument\FinancialDocumentV1::class, $completeDocJSONInvoice["document"]);
        $this->completeDocReceipt = new Document(FinancialDocument\FinancialDocumentV1::class, $completeDocJSONReceipt["document"]);
        $this->emptyDoc = new Document(FinancialDocument\FinancialDocumentV1::class, $emptyDocJSON["document"]);
        $this->completePage0Invoice = new Page(FinancialDocument\FinancialDocumentV1Document::class, $completeDocJSONInvoice["document"]["inference"]["pages"][0]);
        $this->completePage0Receipt = new Page(FinancialDocument\FinancialDocumentV1Document::class, $completeDocJSONReceipt["document"]["inference"]["pages"][0]);
        $this->completeDocReferenceInvoice = file_get_contents($productDir . "summary_full_invoice.rst");
        $this->completeDocReferenceReceipt = file_get_contents($productDir . "summary_full_receipt.rst");
        $this->completePage0ReferenceInvoice = file_get_contents($productDir . "summary_page0_invoice.rst");
        $this->completePage0ReferenceReceipt = file_get_contents($productDir . "summary_page0_receipt.rst");
    }

    public function testCompleteDocInvoice()
    {
        $this->assertEquals($this->completeDocReferenceInvoice, strval($this->completeDocInvoice));
    }

    public function testCompleteDocReceipt()
    {
        $this->assertEquals($this->completeDocReferenceReceipt, strval($this->completeDocReceipt));
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
        $this->assertEquals(0, count($prediction->taxes));
        $this->assertEquals(0, count($prediction->supplierPaymentDetails));
        $this->assertNull($prediction->supplierName->value);
        $this->assertEquals(0, count($prediction->supplierCompanyRegistrations));
        $this->assertNull($prediction->supplierAddress->value);
        $this->assertNull($prediction->customerName->value);
        $this->assertEquals(0, count($prediction->customerCompanyRegistrations));
        $this->assertNull($prediction->customerAddress->value);
        $this->assertEquals(0, count($prediction->lineItems));
        $this->assertNull($prediction->totalTax->value);
        $this->assertNull($prediction->billingAddress->value);
        $this->assertNull($prediction->documentNumber->value);
        $this->assertEquals("EXPENSE RECEIPT", $prediction->documentType->value);
        $this->assertEquals("EXPENSE RECEIPT", $prediction->documentTypeExtended->value);
        $this->assertNull($prediction->customerId->value);
        $this->assertNull($prediction->tip->value);
        $this->assertNull($prediction->time->value);
    }

    public function testCompletePage0Invoice()
    {
        $this->assertEquals(0, $this->completePage0Invoice->id);
        $this->assertEquals($this->completePage0ReferenceInvoice, strval($this->completePage0Invoice));
    }

    public function testCompletePage0Receipt()
    {
        $this->assertEquals(0, $this->completePage0Receipt->id);
        $this->assertEquals($this->completePage0ReferenceReceipt, strval($this->completePage0Receipt));
    }

}
