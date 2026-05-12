<?php

declare(strict_types=1);

namespace V1\Product\Fr\EnergyBill;

use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Fr\EnergyBill\EnergyBillV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class EnergyBillV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/energy_bill_fra/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(EnergyBillV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(EnergyBillV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->invoiceNumber->value);
        self::assertNull($prediction->contractId->value);
        self::assertNull($prediction->deliveryPoint->value);
        self::assertNull($prediction->invoiceDate->value);
        self::assertNull($prediction->dueDate->value);
        self::assertNull($prediction->totalBeforeTaxes->value);
        self::assertNull($prediction->totalTaxes->value);
        self::assertNull($prediction->totalAmount->value);
        self::assertNull($prediction->energySupplier->address);
        self::assertNull($prediction->energySupplier->name);
        self::assertNull($prediction->energyConsumer->address);
        self::assertNull($prediction->energyConsumer->name);
        self::assertCount(0, $prediction->subscription);
        self::assertCount(0, $prediction->energyUsage);
        self::assertCount(0, $prediction->taxesAndContributions);
        self::assertNull($prediction->meterDetails->meterNumber);
        self::assertNull($prediction->meterDetails->meterType);
        self::assertNull($prediction->meterDetails->unit);
    }
}
