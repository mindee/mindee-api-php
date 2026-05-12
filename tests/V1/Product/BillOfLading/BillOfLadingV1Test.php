<?php

declare(strict_types=1);

namespace V1\Product\BillOfLading;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\BillOfLading\BillOfLadingV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class BillOfLadingV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/bill_of_lading/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(BillOfLadingV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(BillOfLadingV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->billOfLadingNumber->value);
        self::assertNull($prediction->shipper->address);
        self::assertNull($prediction->shipper->email);
        self::assertNull($prediction->shipper->name);
        self::assertNull($prediction->shipper->phone);
        self::assertNull($prediction->consignee->address);
        self::assertNull($prediction->consignee->email);
        self::assertNull($prediction->consignee->name);
        self::assertNull($prediction->consignee->phone);
        self::assertNull($prediction->notifyParty->address);
        self::assertNull($prediction->notifyParty->email);
        self::assertNull($prediction->notifyParty->name);
        self::assertNull($prediction->notifyParty->phone);
        self::assertNull($prediction->carrier->name);
        self::assertNull($prediction->carrier->professionalNumber);
        self::assertNull($prediction->carrier->scac);
        self::assertCount(0, $prediction->carrierItems);
        self::assertNull($prediction->portOfLoading->value);
        self::assertNull($prediction->portOfDischarge->value);
        self::assertNull($prediction->placeOfDelivery->value);
        self::assertNull($prediction->dateOfIssue->value);
        self::assertNull($prediction->departureDate->value);
    }
}
