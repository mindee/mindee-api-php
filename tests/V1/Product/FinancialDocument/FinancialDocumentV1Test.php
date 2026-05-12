<?php

declare(strict_types=1);

namespace V1\Product\FinancialDocument;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Parsing\Common\Page;
use Mindee\V1\Product\FinancialDocument\FinancialDocumentV1;
use Mindee\V1\Product\FinancialDocument\FinancialDocumentV1Document;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

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
        $productDir = TestingUtilities::getV1DataDir() . "/products/financial_document/response_v1/";
        $completeDocFileInvoice = file_get_contents($productDir . "complete_invoice.json");
        $completeDocFileReceipt = file_get_contents($productDir . "complete_receipt.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSONInvoice = json_decode($completeDocFileInvoice, true);
        $completeDocJSONReceipt = json_decode($completeDocFileReceipt, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDocInvoice = new Document(FinancialDocumentV1::class, $completeDocJSONInvoice["document"]);
        $this->completeDocReceipt = new Document(FinancialDocumentV1::class, $completeDocJSONReceipt["document"]);
        $this->emptyDoc = new Document(FinancialDocumentV1::class, $emptyDocJSON["document"]);
        $this->completePage0Invoice = new Page(FinancialDocumentV1Document::class, $completeDocJSONInvoice["document"]["inference"]["pages"][0]);
        $this->completePage0Receipt = new Page(FinancialDocumentV1Document::class, $completeDocJSONReceipt["document"]["inference"]["pages"][0]);
        $this->completeDocReferenceInvoice = file_get_contents($productDir . "summary_full_invoice.rst");
        $this->completeDocReferenceReceipt = file_get_contents($productDir . "summary_full_receipt.rst");
        $this->completePage0ReferenceInvoice = file_get_contents($productDir . "summary_page0_invoice.rst");
        $this->completePage0ReferenceReceipt = file_get_contents($productDir . "summary_page0_receipt.rst");
    }

    public function testCompleteDocInvoice(): void
    {
        self::assertSame($this->completeDocReferenceInvoice, (string) ($this->completeDocInvoice));
    }

    public function testCompleteDocReceipt(): void
    {
        self::assertSame($this->completeDocReferenceReceipt, (string) ($this->completeDocReceipt));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->locale->value);
        self::assertNull($prediction->invoiceNumber->value);
        self::assertCount(0, $prediction->referenceNumbers);
        self::assertNull($prediction->date->value);
        self::assertNull($prediction->dueDate->value);
        self::assertNull($prediction->totalNet->value);
        self::assertNull($prediction->totalAmount->value);
        self::assertCount(0, $prediction->taxes);
        self::assertCount(0, $prediction->supplierPaymentDetails);
        self::assertNull($prediction->supplierName->value);
        self::assertCount(0, $prediction->supplierCompanyRegistrations);
        self::assertNull($prediction->supplierAddress->value);
        self::assertNull($prediction->customerName->value);
        self::assertCount(0, $prediction->customerCompanyRegistrations);
        self::assertNull($prediction->customerAddress->value);
        self::assertCount(0, $prediction->lineItems);
        self::assertNull($prediction->totalTax->value);
        self::assertNull($prediction->billingAddress->value);
        self::assertNull($prediction->documentNumber->value);
        self::assertSame("EXPENSE RECEIPT", $prediction->documentType->value);
        self::assertSame("EXPENSE RECEIPT", $prediction->documentTypeExtended->value);
        self::assertNull($prediction->customerId->value);
        self::assertNull($prediction->tip->value);
        self::assertNull($prediction->time->value);
    }

    public function testCompletePage0Invoice(): void
    {
        self::assertSame(0, $this->completePage0Invoice->id);
        self::assertSame($this->completePage0ReferenceInvoice, (string) ($this->completePage0Invoice));
    }

    public function testCompletePage0Receipt(): void
    {
        self::assertSame(0, $this->completePage0Receipt->id);
        self::assertSame($this->completePage0ReferenceReceipt, (string) ($this->completePage0Receipt));
    }

}
