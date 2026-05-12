<?php

declare(strict_types=1);

namespace V1\Product\Invoice;

use Mindee\Product\Invoice;
use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Invoice\InvoiceV4;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class InvoiceV4Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/invoices/response_v4/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(InvoiceV4::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(InvoiceV4::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->locale->value);
        self::assertNull($prediction->invoiceNumber->value);
        self::assertNull($prediction->poNumber->value);
        self::assertCount(0, $prediction->referenceNumbers);
        self::assertNull($prediction->date->value);
        self::assertNull($prediction->dueDate->value);
        self::assertNull($prediction->paymentDate->value);
        self::assertNull($prediction->totalNet->value);
        self::assertNull($prediction->totalAmount->value);
        self::assertNull($prediction->totalTax->value);
        self::assertCount(0, $prediction->taxes);
        self::assertCount(0, $prediction->supplierPaymentDetails);
        self::assertNull($prediction->supplierName->value);
        self::assertCount(0, $prediction->supplierCompanyRegistrations);
        self::assertNull($prediction->supplierAddress->value);
        self::assertNull($prediction->supplierPhoneNumber->value);
        self::assertNull($prediction->supplierWebsite->value);
        self::assertNull($prediction->supplierEmail->value);
        self::assertNull($prediction->customerName->value);
        self::assertCount(0, $prediction->customerCompanyRegistrations);
        self::assertNull($prediction->customerAddress->value);
        self::assertNull($prediction->customerId->value);
        self::assertNull($prediction->shippingAddress->value);
        self::assertNull($prediction->billingAddress->value);
        self::assertCount(0, $prediction->lineItems);
    }
}
