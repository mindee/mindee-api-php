<?php

declare(strict_types=1);

namespace V1\Product\DeliveryNote;

use Mindee\Product\DeliveryNote;
use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\DeliveryNote\DeliveryNoteV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class DeliveryNoteV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/delivery_notes/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(DeliveryNoteV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(DeliveryNoteV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->deliveryDate->value);
        self::assertNull($prediction->deliveryNumber->value);
        self::assertNull($prediction->supplierName->value);
        self::assertNull($prediction->supplierAddress->value);
        self::assertNull($prediction->customerName->value);
        self::assertNull($prediction->customerAddress->value);
        self::assertNull($prediction->totalAmount->value);
    }
}
