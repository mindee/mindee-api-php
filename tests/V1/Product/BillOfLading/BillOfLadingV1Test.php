<?php

namespace V1\Product\BillOfLading;

use Mindee\Parsing\Common\Document;
use Mindee\Product\BillOfLading;
use PHPUnit\Framework\TestCase;

class BillOfLadingV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = \TestingUtilities::getV1DataDir() . "/products/bill_of_lading/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(BillOfLading\BillOfLadingV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(BillOfLading\BillOfLadingV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->billOfLadingNumber->value);
        $this->assertNull($prediction->shipper->address);
        $this->assertNull($prediction->shipper->email);
        $this->assertNull($prediction->shipper->name);
        $this->assertNull($prediction->shipper->phone);
        $this->assertNull($prediction->consignee->address);
        $this->assertNull($prediction->consignee->email);
        $this->assertNull($prediction->consignee->name);
        $this->assertNull($prediction->consignee->phone);
        $this->assertNull($prediction->notifyParty->address);
        $this->assertNull($prediction->notifyParty->email);
        $this->assertNull($prediction->notifyParty->name);
        $this->assertNull($prediction->notifyParty->phone);
        $this->assertNull($prediction->carrier->name);
        $this->assertNull($prediction->carrier->professionalNumber);
        $this->assertNull($prediction->carrier->scac);
        $this->assertEquals(0, count($prediction->carrierItems));
        $this->assertNull($prediction->portOfLoading->value);
        $this->assertNull($prediction->portOfDischarge->value);
        $this->assertNull($prediction->placeOfDelivery->value);
        $this->assertNull($prediction->dateOfIssue->value);
        $this->assertNull($prediction->departureDate->value);
    }
}
