<?php

declare(strict_types=1);

namespace V1\Product\Receipt;

use Mindee\Product\Receipt;
use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Receipt\ReceiptV5;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class ReceiptV5Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/expense_receipts/response_v5/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(ReceiptV5::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(ReceiptV5::class, $emptyDocJSON["document"]);
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
        self::assertNull($prediction->date->value);
        self::assertNull($prediction->time->value);
        self::assertNull($prediction->totalAmount->value);
        self::assertNull($prediction->totalNet->value);
        self::assertNull($prediction->totalTax->value);
        self::assertNull($prediction->tip->value);
        self::assertCount(0, $prediction->taxes);
        self::assertNull($prediction->supplierName->value);
        self::assertCount(0, $prediction->supplierCompanyRegistrations);
        self::assertNull($prediction->supplierAddress->value);
        self::assertNull($prediction->supplierPhoneNumber->value);
        self::assertNull($prediction->receiptNumber->value);
        self::assertCount(0, $prediction->lineItems);
    }
}
