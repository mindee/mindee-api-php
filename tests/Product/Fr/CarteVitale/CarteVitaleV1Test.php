<?php

namespace Product\Fr\CarteVitale;

use Mindee\Product\Fr\CarteVitale;
use Mindee\Parsing\Common\Document;
use Mindee\Parsing\Common\Page;
use PHPUnit\Framework\TestCase;

class CarteVitaleV1Test extends TestCase
{
    private Document $completeDoc;
    private Document $emptyDoc;
    private string $completeDocReference;

    protected function setUp(): void
    {
        $productDir = (getenv('GITHUB_WORKSPACE') ?: ".") . "/tests/resources/products/carte_vitale/response_v1/";
        $completeDocFile = file_get_contents($productDir . "complete.json");
        $emptyDocFile = file_get_contents($productDir . "empty.json");
        $completeDocJSON = json_decode($completeDocFile, true);
        $emptyDocJSON = json_decode($emptyDocFile, true);
        $this->completeDoc = new Document(CarteVitale\CarteVitaleV1::class, $completeDocJSON["document"]);
        $this->emptyDoc = new Document(CarteVitale\CarteVitaleV1::class, $emptyDocJSON["document"]);
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
