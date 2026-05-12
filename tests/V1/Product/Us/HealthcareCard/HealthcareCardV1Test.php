<?php

declare(strict_types=1);

namespace V1\Product\Us\HealthcareCard;

use Mindee\Product\Us\HealthcareCard;
use Mindee\V1\Parsing\Common\Document;
use Mindee\V1\Product\Us\HealthcareCard\HealthcareCardV1;
use PHPUnit\Framework\TestCase;
use TestingUtilities;

class HealthcareCardV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = TestingUtilities::getV1DataDir() . "/products/us_healthcare_cards/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(HealthcareCardV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(HealthcareCardV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc(): void
    {
        self::assertSame($this->completeDocReference, (string) ($this->completeDoc));
    }

    public function testEmptyDoc(): void
    {
        $prediction = $this->emptyDoc->inference->prediction;
        self::assertNull($prediction->companyName->value);
        self::assertNull($prediction->planName->value);
        self::assertNull($prediction->memberName->value);
        self::assertNull($prediction->memberId->value);
        self::assertNull($prediction->issuer80840->value);
        self::assertCount(0, $prediction->dependents);
        self::assertNull($prediction->groupNumber->value);
        self::assertNull($prediction->payerId->value);
        self::assertNull($prediction->rxBin->value);
        self::assertNull($prediction->rxId->value);
        self::assertNull($prediction->rxGrp->value);
        self::assertNull($prediction->rxPcn->value);
        self::assertCount(0, $prediction->copays);
        self::assertNull($prediction->enrollmentDate->value);
    }
}
