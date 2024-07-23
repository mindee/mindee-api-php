<?php

namespace Product\Us\HealthcareCard;

use Mindee\Product\Us\HealthcareCard;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class HealthcareCardV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/us_healthcare_cards/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(HealthcareCard\HealthcareCardV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(HealthcareCard\HealthcareCardV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->companyName->value);
        $this->assertNull($prediction->memberName->value);
        $this->assertNull($prediction->memberId->value);
        $this->assertNull($prediction->issuer80840->value);
        $this->assertEquals(0, count($prediction->dependents));
        $this->assertNull($prediction->groupNumber->value);
        $this->assertNull($prediction->payerId->value);
        $this->assertNull($prediction->rxBin->value);
        $this->assertNull($prediction->rxGrp->value);
        $this->assertNull($prediction->rxPcn->value);
        $this->assertEquals(0, count($prediction->copays));
        $this->assertNull($prediction->enrollmentDate->value);
    }
}
