<?php

namespace V1\Product\Fr\HealthCard;

use Mindee\Parsing\Common\Document;
use Mindee\Product\Fr\HealthCard;
use PHPUnit\Framework\TestCase;

class HealthCardV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = \TestingUtilities::getV1DataDir() . "/products/french_healthcard/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(HealthCard\HealthCardV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(HealthCard\HealthCardV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertEquals(0, count($prediction->givenNames));
        $this->assertNull($prediction->surname->value);
        $this->assertNull($prediction->socialSecurity->value);
        $this->assertNull($prediction->issuanceDate->value);
    }
}
