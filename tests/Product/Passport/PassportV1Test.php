<?php

namespace Product\Passport;

use Mindee\Product\Passport;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class PassportV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/passport/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(Passport\PassportV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(Passport\PassportV1::class, $emptyDocJSON["document"]);
        $this->completeDocReference = file_get_contents($productDir . "summary_full.rst");
    }

    public function testCompleteDoc()
    {
        $this->assertEquals($this->completeDocReference, strval($this->completeDoc));
    }

    public function testEmptyDoc()
    {
        $prediction = $this->emptyDoc->inference->prediction;
        $this->assertNull($prediction->country->value);
        $this->assertNull($prediction->idNumber->value);
        $this->assertEquals(0, count($prediction->givenNames));
        $this->assertNull($prediction->surname->value);
        $this->assertNull($prediction->birthDate->value);
        $this->assertNull($prediction->birthPlace->value);
        $this->assertNull($prediction->gender->value);
        $this->assertNull($prediction->issuanceDate->value);
        $this->assertNull($prediction->expiryDate->value);
        $this->assertNull($prediction->mrz1->value);
        $this->assertNull($prediction->mrz2->value);
    }
}
