<?php

namespace V1\Product\Fr\EnergyBill;

use Mindee\Parsing\Common\Document;
use Mindee\Product\Fr\EnergyBill;
use PHPUnit\Framework\TestCase;

class EnergyBillV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = \TestingUtilities::getV1DataDir() . "/products/energy_bill_fra/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(EnergyBill\EnergyBillV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(EnergyBill\EnergyBillV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->invoiceNumber->value);
        $this->assertNull($prediction->contractId->value);
        $this->assertNull($prediction->deliveryPoint->value);
        $this->assertNull($prediction->invoiceDate->value);
        $this->assertNull($prediction->dueDate->value);
        $this->assertNull($prediction->totalBeforeTaxes->value);
        $this->assertNull($prediction->totalTaxes->value);
        $this->assertNull($prediction->totalAmount->value);
        $this->assertNull($prediction->energySupplier->address);
        $this->assertNull($prediction->energySupplier->name);
        $this->assertNull($prediction->energyConsumer->address);
        $this->assertNull($prediction->energyConsumer->name);
        $this->assertEquals(0, count($prediction->subscription));
        $this->assertEquals(0, count($prediction->energyUsage));
        $this->assertEquals(0, count($prediction->taxesAndContributions));
        $this->assertNull($prediction->meterDetails->meterNumber);
        $this->assertNull($prediction->meterDetails->meterType);
        $this->assertNull($prediction->meterDetails->unit);
    }
}
