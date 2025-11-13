<?php

namespace V1\Product\DeliveryNote;

use Mindee\Parsing\Common\Document;
use Mindee\Product\DeliveryNote;
use PHPUnit\Framework\TestCase;

class DeliveryNoteV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = \TestingUtilities::getV1DataDir() . "/products/delivery_notes/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(DeliveryNote\DeliveryNoteV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(DeliveryNote\DeliveryNoteV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->deliveryDate->value);
        $this->assertNull($prediction->deliveryNumber->value);
        $this->assertNull($prediction->supplierName->value);
        $this->assertNull($prediction->supplierAddress->value);
        $this->assertNull($prediction->customerName->value);
        $this->assertNull($prediction->customerAddress->value);
        $this->assertNull($prediction->totalAmount->value);
    }
}
