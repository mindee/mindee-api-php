<?php

namespace V1\Product\InternationalId;

use Mindee\Parsing\Common\Document;
use Mindee\Product\InternationalId;
use PHPUnit\Framework\TestCase;

class InternationalIdV2Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = \TestingUtilities::getV1DataDir() . "/products/international_id/response_v2/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(InternationalId\InternationalIdV2::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(InternationalId\InternationalIdV2::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->documentNumber->value);
        $this->assertEquals(0, count($prediction->surnames));
        $this->assertEquals(0, count($prediction->givenNames));
        $this->assertNull($prediction->sex->value);
        $this->assertNull($prediction->birthDate->value);
        $this->assertNull($prediction->birthPlace->value);
        $this->assertNull($prediction->nationality->value);
        $this->assertNull($prediction->personalNumber->value);
        $this->assertNull($prediction->countryOfIssue->value);
        $this->assertNull($prediction->stateOfIssue->value);
        $this->assertNull($prediction->issueDate->value);
        $this->assertNull($prediction->expiryDate->value);
        $this->assertNull($prediction->address->value);
        $this->assertNull($prediction->mrzLine1->value);
        $this->assertNull($prediction->mrzLine2->value);
        $this->assertNull($prediction->mrzLine3->value);
    }
}
